# AutoPost to Social Media Web App

This web application is a **social media automation tool** built using **Laravel 11** and **FilamentPHP**. It allows users to schedule posts and automatically publish them to Twitter and Facebook. Designed with simplicity and efficiency in mind, the app streamlines social media management tasks for businesses, influencers, and individuals.

---

## **Features**

- **Post Scheduling**: Schedule posts with content and images for future publication.
- **Automated Posting**: Automatically publish posts to:
  - **Twitter (X.com)**
  - **Facebook Pages**
- **Tag Integration**: Format and append hashtags to post content dynamically.
- **Error Handling**: Comprehensive logging and error management for failed posts.
- **Notifications**: Notify users about successful or failed scheduled posts.
- **User Management**: Built-in user management powered by **FilamentPHP**.
- **Responsive UI**: A clean and user-friendly interface for managing scheduled posts.

---

## **Tech Stack**

- **Framework**: Laravel 11
- **Admin Panel**: FilamentPHP
- **Social Media API Integration**:
  - **Twitter API**: Integration using `Noweh\TwitterApi\Client`.
  - **Facebook Graph API**: Integration with `php-graph-sdk`.
- **Database**: MySQL (or your preferred Laravel-supported database).
- **Notifications**: FilamentPHP Notification System.
- **Frontend**: TailwindCSS for styling.
- **Task Scheduling**: Laravel Console Commands and Scheduler.

---

## **Installation**

### **1. Clone the Repository**

```bash
git clone https://github.com/fawaziwalewa/autopost-social-media.git
cd autopost-social-media
```

### **2. Install Dependencies**

```bash
composer install
npm install
npm run build
```

### **3. Configure the Environment**

Rename the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Update the following keys in your `.env` file:

```env
APP_NAME=AutoPost
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Optional (The settings below are available in the dashboard)

TWITTER_API_KEY=your_twitter_api_key
TWITTER_API_SECRET_KEY=your_twitter_api_secret_key
TWITTER_ACCESS_TOKEN=your_twitter_access_token
TWITTER_ACCESS_TOKEN_SECRET=your_twitter_access_token_secret
TWITTER_AUTO_POST=true

FACEBOOK_APP_ID=your_facebook_app_id
FACEBOOK_APP_SECRET=your_facebook_app_secret
FACEBOOK_ACCESS_TOKEN=your_facebook_page_access_token
FACEBOOK_PAGE_ID=your_facebook_page_id
FACEBOOK_AUTO_POST=true
```

### **4. Run Migrations**

Set up the database schema:

```bash
php artisan migrate
```

### **5. Generate the Application Key**

```bash
php artisan key:generate
```

### **6. Create an Admin User**

You can create a new user account with the following command:

```bash
php artisan make:filament-user
```

### **7. Start the Application**

Run the development server:

```bash
composer run dev
```

---

## **Usage**

### **1. Scheduling Posts**

1. Log in to the admin panel.
2. Navigate to the **Posts** section.
3. Create a new post by specifying:
   - **Content**: Text to be posted.
   - **Image** (optional): Upload an image.
   - **Tags**: Add hashtags for better reach.
   - **Scheduled Date/Time**: When the post should go live.

### **2. Automating Posts**

The app automatically runs the `AutoPostToSocialMedia` command to publish scheduled posts. Ensure the Laravel Scheduler is active:

```bash
php artisan schedule:work
```

### **3. Viewing Logs**

Check the application logs for any errors or debug information:

```bash
storage/logs/laravel.log
```

---

## **Built-in Notifications**

- **Success Notifications**: Notifies users when posts are successfully published.
- **Error Notifications**: Alerts users if a scheduled post fails.

---

## **Customizing the App**

- **API Keys**: Update the `.env` file with new API keys when necessary.
- **Admin Panel**: Use FilamentPHP to customize the admin dashboard.
- **Post Logic**: Modify the logic for posting in `App\Console\Commands\AutoPostToSocialMedia`.

---

## **Contributing**

1. Fork the repository.

2. Create a new branch:

    ```bash
    git checkout -b feature-name
    ```

3. Commit your changes:

   ```bash
   git commit -m "Add feature-name"
   ```

4. Push the branch:

   ```bash
   git push origin feature-name
   ```

5. Open a pull request.

---

## **License**

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## **Acknowledgments**

- [Laravel](https://laravel.com/)
- [FilamentPHP](https://filamentphp.com/)
- [Twitter API](https://developer.twitter.com/)
- [Facebook Graph API](https://developers.facebook.com/docs/graph-api/)

---

## **Contact**

For questions or support, please contact:

- **Email**: <fawaziwalewa@gmail.com>
- **GitHub**: [fawaziwalewa](https://github.com/fawaziwalewa)
- **Twitter**: [@IwalewaFawaz](https://twitter.com/IwalewaFawaz)
- **LinkedIn**: [Fawaz Iwalewa](https://www.linkedin.com/in/fawaz-iwalewa)
