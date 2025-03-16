import sys
sys.path.append("/www/htdocs/v134538/verworfen.at/python-modules")
import requests
from bs4 import BeautifulSoup
import pymysql
import datetime
import re
from dotenv import load_dotenv
import os

print("Python version:", sys.version)
print("Python executable:", sys.executable)

load_dotenv()

db_host = os.getenv("DB_HOST")
db_user = os.getenv("DB_USER")
db_password = os.getenv("DB_PASSWORD")
db_name = os.getenv("DB_NAME")

def clean_headline(text):
    text = re.sub(r"\s+", " ", text.strip())  # Mehrere Leerzeichen durch eines ersetzen
    text = re.sub(r"(?<=\S):(?=\S)", ": ", text)  # Leerzeichen nur hinzufügen, wenn `:` direkt zwischen Zeichen steht
    return text

response = requests.get("https://news.orf.at")
soup = BeautifulSoup(response.text, "html.parser")

headlines = {}

for container in soup.select("div.oon-grid-texts-container, div.oon-grid-texts-container-mobile"):
    headlines_list = [h1.get_text(strip=True) for h1 in container.find_all("h1")]
    
    if headlines_list:
        full_headline = " ".join(headlines_list)
        full_headline = clean_headline(full_headline)
        
        url_tag = container.find_parent("a", href=True)
        if url_tag:
            url = url_tag["href"]
            
            if full_headline and len(full_headline) > 10:
                headlines[url] = full_headline

for h3 in soup.select("h3.ticker-story-headline a"):
    headline = clean_headline(h3.get_text())
    url = h3["href"]
    
    if headline and len(headline) > 10:  
        headlines[url] = headline


with pymysql.connect(host=db_host, user=db_user, password=db_password, db=db_name, charset="utf8mb4") as conn:
    with conn.cursor() as cursor:
        for url, headline in headlines.items():
            cursor.execute("SELECT new_headline FROM headline_changes WHERE article_url=%s ORDER BY detected_at DESC LIMIT 1", (url,))
            result = cursor.fetchone()
            
            if result and result[0] != headline:
                cursor.execute("""
                    INSERT INTO headline_changes (original_headline, new_headline, article_url, detected_at)
                    VALUES (%s, %s, %s, NOW())
                """, (result[0], headline, url))
            elif not result:
                cursor.execute("""
                    INSERT INTO headline_changes (original_headline, new_headline, article_url, detected_at)
                    VALUES (%s, %s, %s, NOW())
                """, (headline, headline, url))

        conn.commit()
