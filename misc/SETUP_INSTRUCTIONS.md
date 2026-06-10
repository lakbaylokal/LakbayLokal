# LakbayLokal Authentication Setup

## What Was Changed

1. **Added Users Table** to `lakbaylokal.sql`
   - Stores user accounts with encrypted passwords
   - Includes sample test accounts (see credentials below)

2. **Updated `api_auth.php`** to use database instead of session storage
   - Login now queries the users table
   - Signup saves new users to the database permanently
   - All user data persists across browser sessions

## How to Setup

### Step 1: Import the Database

1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
2. Click on **Import** tab
3. Choose the file: `misc/lakbaylokal.sql`
4. Click **Import** button
5. You should see the `lakbaylokal` database created with all tables

### Step 2: Test the Authentication

Use these test accounts to login:

| Email | Password | Role |
|-------|----------|------|
| admin@lakbaylokal.com | password123 | admin |
| user@test.com | password123 | user |
| juan@example.com | password123 | user |

### Step 3: Create New Accounts

Use the signup form on your app to create additional user accounts. These will be automatically saved to the database.

## Database Connection

The app uses the credentials in `config/db.php`:
- **Host**: localhost
- **User**: root
- **Password**: (empty by default in XAMPP)
- **Database**: lakbaylokal

If your XAMPP setup uses different credentials, update `config/db.php`.

## How It Works

### Login Flow
1. User submits email & password
2. API queries `users` table by email
3. Password verified using bcrypt
4. Session created if credentials match

### Signup Flow
1. User submits name, email & password
2. API validates the data
3. Email checked for duplicates
4. Password hashed with bcrypt
5. New user inserted into `users` table
6. Session created automatically

### Logout Flow
1. Session cleared
2. User returned to login page

## Passwords & Security

- All passwords are hashed with **bcrypt** (PASSWORD_BCRYPT)
- Never stored in plain text
- Safe against rainbow table attacks
- Each password is unique even for identical strings

## Testing Credentials

**Admin Account:**
- Email: `admin@lakbaylokal.com`
- Password: `password123`

**Regular Users:**
- Email: `user@test.com` | Password: `password123`
- Email: `juan@example.com` | Password: `password123`

## Need to Reset?

If you want to start fresh:
1. Go to phpMyAdmin
2. Select the `lakbaylokal` database
3. Drop the `users` table
4. Re-import the SQL file

All user accounts will be reset to the sample data.
