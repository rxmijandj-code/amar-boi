আমার বই — PHP + MySQL ওয়েবসাইট
================================

এই ZIP-এ User Website, Admin Panel, PHP API, MySQL schema এবং Render Docker config আছে।

ফিচার
- User Website সাদা / Light theme
- Admin Panel ডার্ক theme
- আপনার দেওয়া “আমার বই” লোগো assets/logo.png হিসেবে যুক্ত
- Admin থেকে ওয়েবসাইটের নাম, tagline, logo URL ও পরিচিতি পরিবর্তন
- Admin থেকে কনটেন্ট যোগ, সম্পাদনা, মুছে ফেলা ও User-দের জন্য দেখানো/লুকানো
- কুরআনের আরবি লেখা, বাংলা অনুবাদ, সূরা/অধ্যায়, অডিও URL
- Admin থেকে Home Slider যোগ/সম্পাদনা/মুছে ফেলা
- Admin-only Global Reciter নাম ও Audio URL: User-রা একই সেটিং পাবে
- লাইভ সময় ও তারিখ
- PHP session login, CSRF token, PDO prepared statements

গুরুত্বপূর্ণ: আরবি আয়াত, অনুবাদ, সূরা ডেটা বা অডিও আগে থেকে যোগ করা নেই। সঠিক উৎস থেকে যাচাই করে Admin Panel-এ তুমি নিজে যোগ করবে। Global Audio URL সরাসরি playable MP3/অডিও ফাইলের URL হওয়া দরকার; শুধু Quran.com পেজের URL দিলে audio player নাও চলতে পারে।

প্রথমে যা করতে হবে
1) একটি MySQL database তৈরি করো (utf8mb4 সমর্থন থাকতে হবে)।
2) Database-এর phpMyAdmin/SQL console-এ schema.sql ফাইলের সম্পূর্ণ SQL import করো। এতে প্রয়োজনীয় tables ও “আমার বই” default settings তৈরি হবে।
3) Hosting-এর Environment Variables-এ DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD বসাও।
4) Admin password hash তৈরি করো (নিজের কম্পিউটার/টার্মিনালে PHP থাকলে):
   php -r "echo password_hash('তোমার-নিজের-দীর্ঘ-পাসওয়ার্ড', PASSWORD_DEFAULT), PHP_EOL;"
   যে hash আসবে সেটি Render Environment-এ ADMIN_PASSWORD_HASH নামে বসাও।
   ADMIN_PASSWORD_HASH ব্যবহার করাই ভালো। ADMIN_PASSWORD plain text fallback কেবল পরীক্ষার জন্য; production-এ ব্যবহার না করাই ভালো।
5) GitHub-এ ZIP extract করে সব ফাইল upload করো। .env.example-এ আসল password বসিয়ে GitHub-এ upload করবে না।
6) Render-এ New Web Service > GitHub repo connect করো; Runtime Docker / Dockerfile ব্যবহার হবে। render.yaml Blueprint হিসেবেও ব্যবহার করা যায়।
7) Deploy শেষে https://তোমার-সাইট/admin.html খুলে Admin password দিয়ে login করো।

Database কোথায় রাখবে?
Render Web Service নিজে MySQL database দেয় না। আলাদা managed MySQL provider থেকে database নিতে হবে; সেখানকার host, port, database name, username, password Render Environment-এ বসাতে হবে। Provider-এর connection allowlist/network rules Render থেকে connection অনুমোদন করতে হবে। Render Free web service sleep করতে পারে; external DB-এর plan/limits আলাদা হতে পারে।

ফাইল তালিকা
index.html, style.css, app.js       — User website
admin.html, admin.css, admin.js     — Admin panel
api.php, config.php                 — PHP backend/API
schema.sql                          — MySQL tables ও initial settings
Dockerfile, render.yaml             — Render deployment
.env.example                        — Environment variable নমুনা
assets/logo.png                     — “আমার বই” লোগো

নিরাপত্তা
- Database credentials কেবল Hosting Environment-এ রাখবে; GitHub-এ নয়।
- ADMIN_PASSWORD_HASH ব্যবহার করবে এবং শক্তিশালী আলাদা password রাখবে।
- Database-এ public write permission খোলা নেই; সব save/delete request Admin session ও CSRF token চায়।
- Admin URL গোপন রাখাই নিরাপত্তা নয়; password ও server-side session-ই access control।
- HTTPS ব্যবহার করবে।

নোট
এটি HTML/CSS/JavaScript + PHP + MySQL ওয়েব অ্যাপ; APK নয়। ZIP-টি local browser/hosting-এ সম্পূর্ণ production smoke-test করা হয়নি, কারণ চালাতে বাস্তব MySQL service ও Hosting Environment দরকার। Deploy করার আগে SQL import এবং environment values অবশ্যই ঠিকভাবে সেট করতে হবে।
