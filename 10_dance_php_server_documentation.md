<div dir="rtl">

# 10Dance – PHP Server API

מסמך זה מתאר את שרת ה־PHP של פרויקט **10Dance**: מבנה הפרויקט, אופן ההגדרות, וחוזי ה־API (endpoints).

---

## 📌 סקירה כללית

שרת ה־PHP משמש כ־REST API עבור קליינט React (Vite).

- טכנולוגיות: **PHP 8+, MySQL, PDO**
- סגנון: API מבוסס קבצי PHP
- אימות: ללא (נכון לשלב זה)
- פורמט תגובות: JSON

---

## 📁 מבנה תיקיות

<div dir="ltr">

```
/project-root
│
├── config.php              # קובץ קונפיגורציה (מחוץ ל-public_html בפרודקשן)
│
├── public_html/
│   └── 10dance-api/
│       ├── get-all-events.php
│       ├── get-all-attendees.php
│       ├── add-attendee.php
│       ├── update-attendee.php
│       ├── delete-attendee.php
│       │
│       ├── 2-utils/
│       │   └── Database.php
│       │
│       └── 3-logic/
│           └── server-logic.php

```

<div dir="ltr">

---

## ⚙️ קובץ config.php

`config.php` אחראי על פרטי החיבור למסד הנתונים.

<div dir="ltr">

```php
<?php
return [
  'host' => 'DB_HOST',
  'name' => 'DB_NAME',
  'user' => 'DB_USER',
  'pass' => 'DB_PASS',
];
<div dir="ltr">

```

📍 בפרודקשן: הקובץ נמצא **מחוץ ל־public_html**

---

## 🧱 Database.php

מחלקה האחראית על:

- טעינת קובץ `config.php`
- יצירת חיבור PDO
- אספקת `$this->conn` לשכבת הלוגיקה

טעינת הקונפיג מתבצעת כך:

<div dir="ltr">

```php
$localConfig = __DIR__ . '/config.php';
$prodConfig  = dirname(__DIR__, 2) . '/config.php';

$configPath = file_exists($localConfig) ? $localConfig : $prodConfig;
$config = require $configPath;
<div dir="ltr">

```

---

## 🌐 CORS

כל endpoint מתחיל בהגדרות CORS:

<div dir="ltr">

```php
header("Access-Control-Allow-Origin: https://your-frontend-domain.com");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}
<div dir="ltr">

```

---

## 📡 API Endpoints

### 🔹 GET /get-all-events.php

מחזיר את כל האירועים.

**Response:**

<div dir="ltr">

```json
[
  {
    "id": 1,
    "name": "Event name",
    "date": "2025-01-01"
  }
]
<div dir="ltr">

```

---

### 🔹 GET /get-all-attendees.php?tableId={id}

מחזיר את כל המשתתפים של אירוע.

**Query Params:**

- `tableId` – מזהה האירוע

---

### 🔹 POST /add-attendee.php

הוספת משתתף חדש.

**Body (JSON):**

<div dir="ltr">

```json
{
  "tzId": "123456789",
  "firstName": "John",
  "lastName": "Doe",
  "institute": "Institute name",
  "eventTable": "event_1"
}
<div dir="ltr">

```

---

### 🔹 PUT /update-attendee.php

עדכון פרטי משתתף.

---

### 🔹 DELETE /delete-attendee.php

מחיקת משתתף.

---

## 🧪 טיפול בשגיאות

- סטטוסי HTTP תקניים (200 / 201 / 204 / 404 / 500)
- תגובת JSON:
<div dir="ltr">

```json
{ "error": "message" }
<div dir="ltr">

```

---

## 🚀 עבודה עם React (Vite)

בקליינט:

`.env.local`

<div dir="ltr">

```env
VITE_API_URL=http://localhost/10dance-api
<div dir="ltr">

```

שימוש:

<div dir="ltr">

```js
axios.get(`${import.meta.env.VITE_API_URL}/get-all-events.php`);
<div dir="ltr">

```

---

## 🔐 אבטחה – Best Practices

- אין סיסמאות בפרונט
- `config.php` מחוץ ל־public_html
- אין `var_dump / echo` בפרודקשן
- `display_errors = Off`

---

## 🧭 סיכום

השרת בנוי בצורה מודולרית, פשוטה וקריאה:

- הפרדה בין DB, לוגיקה ו־endpoints
- ניתוב פשוט ללא framework
- קל להרחבה (auth, roles, staging)

---

✍️ תיעוד זה נוצר עבור פרויקט 10Dance

</div>
