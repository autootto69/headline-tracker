# Headline Tracker

A Python- and PHP-based open-source project for automatically detecting and storing changes to headlines on Austria's public broadcasting  website ([news.orf.at](https://news.orf.at)).

## Features
- **Automated Monitoring**: The script `checker.py` checks ORF headlines every 10 minutes and stores them in a MySQL database.
- **Change Detection**: Compares headlines with previous versions and calculates the Levenshtein distance.
- **Web Interface (PHP)**: Displays the last 20 or 100 changes with filters for minor changes (<5 characters) or sports news.
- **Archiving**: `generate_archives.php` automatically creates monthly archive pages.
- **Security**: `.htaccess` blocks external access to `.env` and `db_config.php`.

## Installation & Setup

### Requirements
- **Python 3** with `requests`, `BeautifulSoup4`, `pymysql`, `dotenv`
- **PHP 7+** with MySQL support
- **MySQL database** with the `headline_changes` table
- **Git** for version control

### 1. Clone the Repository
```sh
git clone https://github.com/autootto69/headline-tracker.git
cd headline-tracker
```

### 2. Install Python Dependencies
```sh
pip install -r requirements.txt
```

### 3. Create a `.env` File
Create a `.env` file with your MySQL credentials:
```ini
DB_HOST=your-database-host
DB_USER=your-username
DB_PASSWORD=your-password
DB_NAME=your-database-name
```

### 4. Add Levenshtein Function to MySQL (Required for Change Detection)
Source: [GitHub Gist by Kovah](https://gist.github.com/Kovah/df90d336478a47d869b9683766cff718)
```sql
DELIMITER $$
DROP FUNCTION IF EXISTS LEVENSHTEIN $$
CREATE FUNCTION LEVENSHTEIN(s1 VARCHAR(255) CHARACTER SET utf8, s2 VARCHAR(255) CHARACTER SET utf8)
  RETURNS INT
  DETERMINISTIC
  BEGIN
    DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT;
    DECLARE s1_char CHAR CHARACTER SET utf8;
    DECLARE cv0, cv1 VARBINARY(256);

    SET s1_len = CHAR_LENGTH(s1),
        s2_len = CHAR_LENGTH(s2),
        cv1 = 0x00,
        j = 1,
        i = 1,
        c = 0;

    IF (s1 = s2) THEN
      RETURN (0);
    ELSEIF (s1_len = 0) THEN
      RETURN (s2_len);
    ELSEIF (s2_len = 0) THEN
      RETURN (s1_len);
    END IF;

    WHILE (j <= s2_len) DO
      SET cv1 = CONCAT(cv1, CHAR(j)),
          j = j + 1;
    END WHILE;

    WHILE (i <= s1_len) DO
      SET s1_char = SUBSTRING(s1, i, 1),
          c = i,
          cv0 = CHAR(i),
          j = 1;

      WHILE (j <= s2_len) DO
        SET c = c + 1,
            cost = IF(s1_char = SUBSTRING(s2, j, 1), 0, 1);

        SET c_temp = ORD(SUBSTRING(cv1, j, 1)) + cost;
        IF (c > c_temp) THEN
          SET c = c_temp;
        END IF;

        SET c_temp = ORD(SUBSTRING(cv1, j+1, 1)) + 1;
        IF (c > c_temp) THEN
          SET c = c_temp;
        END IF;

        SET cv0 = CONCAT(cv0, CHAR(c)),
            j = j + 1;
      END WHILE;

      SET cv1 = cv0,
          i = i + 1;
    END WHILE;

    RETURN (c);
  END $$
DELIMITER ;
```

### 5. Set Up MySQL Database
```sql
CREATE TABLE headline_changes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_headline TEXT NOT NULL,
    new_headline TEXT NOT NULL,
    article_url VARCHAR(255) NOT NULL,
    detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    levenshtein_distance INT
);

DELIMITER //
CREATE TRIGGER before_insert_headline_changes
BEFORE INSERT ON headline_changes
FOR EACH ROW
BEGIN
    SET NEW.levenshtein_distance = LEVENSHTEIN(NEW.original_headline, NEW.new_headline);
END;
//
DELIMITER ;
```

### 6. Create `db_config.php`
```php
<?php
$DB_HOST = "your_server";
$DB_USER = "your_user";
$DB_PASSWORD = "your_password";
$DB_NAME = "your_database";
?>
```

### 7. Set Up Cron Jobs

The following cron jobs automate the execution of the scripts:

#### **Run `checker.py` every 10 minutes**
This script monitors headlines and detects changes.
```sh
*/10 * * * * /usr/bin/python3 /path/to/checker.py >> /var/log/checker.log 2>&1
```

Some hosting providers only allow PHP-based cron jobs.
```sh
*/10 * * * * /usr/bin/php /path/to/cron_wrapper.php
```

#### **Run `generate_archives.php` on the 1st of each month at 00:01**
This script generates an archive page for the previous month's headline changes.
```sh
1 0 1 * * /usr/bin/php /path/to/generate_archives.php >> /var/log/generate_archives.log 2>&1

## Usage
- **Automatic Execution**: `checker.py` runs via cron jobs.
- **Manual Execution**:
  ```sh
  python3 checker.py
  ```
## **Web Interface**: Open `index.php` to view changes.

## Security
- `.htaccess` blocks `.env` and `db_config.php`.

## License
This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**. See `LICENSE` for details.

---
If you have any questions, contact me via jcz@jczeller.com
