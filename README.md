# 🗳️ Election Management System  
A comprehensive web-based application for managing elections, candidates, and voters with secure voting functionality.

---

## ✨ Features

### 👨‍💼 Admin Features
- 📊 Dashboard with election statistics  
- 🗳️ Create and manage elections  
- 👥 Manage candidates and voters  
- 📈 View election results  
- 🔒 Secure admin authentication  

### 🧑‍💻 Voter Features
- 👤 User-friendly voter dashboard  
- 🗳️ Cast votes in active elections  
- 📅 View upcoming and past elections  
- 🔐 Secure voter authentication  

---

## 🛠️ Tech Stack

### 🎨 Frontend
- HTML5  
- CSS3 (with responsive design)  
- JavaScript  
- Bootstrap / Poppins for styling  

### ⚙️ Backend
- PHP  
- MySQL Database  
- Apache Server  

---

## 🧰 Installation

### 1️⃣ Prerequisites
- XAMPP  
- PHP 7.4 or higher  
- MySQL 5.7 or higher  
- Web browser (Chrome, Firefox, Safari)  

### 2️⃣ Setup

```bash
# Clone the repository
git clone [repository-url]

# Move files to your web server directory
# (e.g., for XAMPP: C:/xampp/htdocs/)

# Import the database
# - Open phpMyAdmin
# - Create a new database named 'vote'
# - Import the voting_system.sql file
```

### 3️⃣ Configuration
- Update database credentials in `config.php`  
- Set proper timezone in PHP configuration  
- Ensure correct file permissions  

---

## 📁 Project Structure

```
├── admin/
│   ├── admin_dashboard.php
│   ├── manage_elections.php
│   ├── manage_candidates.php
│   ├── manage_voters.php
│   ├── view_results.php
│   ├── create_election.php
│   ├── create_candidate.php
│   ├── edit_election.php
│   ├── edit_candidate.php
│   └── header.php
│
├── voter/
│   ├── user_dashboard.php
│   ├── cast_vote.php
│   ├── profile.php
│   ├── view_results.php
│   └── header.php
│
├── config.php  
├── combined.css  
├── index.php  
├── register.php  
└── logout.php  
```

---

## 🚀 Usage

### 🔑 Admin Access
- Login with admin credentials  
- Manage elections, candidates, and voters  
- View election results  

### 🙋‍♂️ Voter Access
- Register as a voter  
- Login to view active elections  
- Cast votes in active elections  

---

## 📱 Responsive Design
- Mobile-friendly interface  
- Desktop optimized  
- Responsive tables and forms  
- Consistent styling across devices  

🎥 **Project Demo**

Watch the live demo video on LinkedIn:  
👉 [Click here to watch on LinkedIn](https://www.linkedin.com/posts/areebamemon_html-css-javascript-activity-7334837182852919299-JIxl?utm_source=share&utm_medium=member_desktop&rcm=ACoAADnaLtIBWm93ZxaE556cUjF5GnYuvFxZwDs)
