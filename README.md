# Laravel Todo List Application

A Laravel application that allows users to manage daily todo lists with recurring todo functionality.

## Features

- **Daily Todo List**: View and manage todos for the current day
- **Recurring Todos**: Set up todos that automatically appear on specific days of the week
- **Authentication**: User registration and login using Laravel Breeze
- **Clean Slate**: Each day starts fresh - old todos don't carry over
- **SQLite Database**: Lightweight database setup

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Create SQLite database:
   ```bash
   touch database/database.sqlite
   ```
5. Configure `.env` file:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/path/to/your/project/database/database.sqlite
   ```
6. Run migrations:
   ```bash
   php artisan migrate
   ```

## Usage

### Starting the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

### Daily Workflow

1. **Login/Register**: Create an account or log in
2. **Today's Todos**: View your todo list for today
3. **Add Todos**: Add new todos for today only
4. **Complete Todos**: Check off completed items
5. **Manage Recurring**: Set up todos that repeat on specific days

### Recurring Todos

1. Navigate to "Recurring Todos" from the navigation menu
2. Add new recurring todos by specifying:
   - Title and description
   - Day of the week (Monday-Sunday)
3. Manage existing recurring todos (activate/deactivate/delete)

### Console Commands

Generate daily todos from recurring todos:
```bash
php artisan todos:generate-daily
```

Generate todos for a specific date:
```bash
php artisan todos:generate-daily --date=2025-09-04
```

Cleanup old todos:
```bash
php artisan todos:cleanup-old --days=7
```

### Automated Scheduling

The application includes scheduled commands that run automatically:
- Daily todo generation: Runs every day at midnight
- Old todo cleanup: Runs weekly to remove todos older than 7 days

To enable scheduling, add this to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Database Structure

### Users
- Standard Laravel user authentication

### Recurring Todos
- `user_id`: Foreign key to users
- `title`: Todo title
- `description`: Optional description
- `day_of_week`: Monday through Sunday
- `is_active`: Enable/disable recurring todo

### Todos
- `user_id`: Foreign key to users
- `title`: Todo title
- `description`: Optional description
- `date`: Date this todo is for
- `is_completed`: Completion status
- `recurring_todo_id`: Optional link to recurring todo

## Architecture

- **Laravel 12**: Latest Laravel framework
- **SQLite**: Lightweight database
- **Blade Templates**: Server-side rendering
- **Tailwind CSS**: Styling framework (via Laravel Breeze)
- **Laravel Breeze**: Authentication scaffolding

## Key Concepts

- **Daily Reset**: Each day is independent - unfinished todos don't carry over
- **Recurring Logic**: Recurring todos generate new daily todos automatically
- **User Isolation**: Each user has their own todos and recurring todos
- **Clean Interface**: Simple, focused interface for daily task management
