# Laravel Todo List Application - Claude Documentation

**IMPORTANT**: Do not run development servers (php artisan serve or npm run dev) as they are already running in the background.

## Project Overview

A Laravel-based daily todo list application with recurring todo functionality. The app focuses on a "fresh start" approach where each day begins with a clean slate, and old todos don't carry over. Users can set up recurring todos that automatically appear on specific days of the week.

**Key Philosophy**: Each day is independent - unfinished todos from previous days are gone, promoting a daily reset mindset.

## Technical Stack

- **Framework**: Laravel 12.28.0
- **Database**: SQLite (lightweight, file-based)
- **Authentication**: Laravel Breeze (Blade templates)
- **Frontend**: Blade templates with Tailwind CSS
- **Build Tools**: Vite, npm
- **PHP Version**: Compatible with PHP 8.x

## Database Schema

### Users Table
- Standard Laravel authentication (comes with Breeze)
- Fields: id, name, email, email_verified_at, password, remember_token, created_at, updated_at

### RecurringTodos Table
```sql
- id (primary key)
- user_id (foreign key to users, cascade delete)
- title (string, required)
- description (text, nullable)
- day_of_week (enum: monday, tuesday, wednesday, thursday, friday, saturday, sunday)
- is_active (boolean, default true)
- created_at, updated_at
```

### Todos Table
```sql
- id (primary key)
- user_id (foreign key to users, cascade delete)
- title (string, required)
- description (text, nullable)
- is_completed (boolean, default false)
- date (date, for which day this todo belongs)
- recurring_todo_id (foreign key to recurring_todos, nullable, cascade delete)
- created_at, updated_at
```

### ActivityTrackers Table
```sql
- id (primary key)
- user_id (foreign key to users, cascade delete)
- name (string, required)
- description (text, nullable)
- last_completed_date (date, nullable)
- frequency_days (unsigned integer, nullable, 1-3650)
- created_at, updated_at
```

## Models and Relationships

### User Model
- `hasMany(Todo::class)`
- `hasMany(RecurringTodo::class)`
- `hasMany(ActivityTracker::class)`

### Todo Model
- `belongsTo(User::class)`
- `belongsTo(RecurringTodo::class)` (nullable - only for generated todos)
- Fillable: user_id, title, description, is_completed, date, recurring_todo_id
- Casts: is_completed (boolean), date (date)

### RecurringTodo Model
- `belongsTo(User::class)`
- `hasMany(Todo::class)`
- Fillable: user_id, title, description, day_of_week, is_active
- Casts: is_active (boolean)

### ActivityTracker Model
- `belongsTo(User::class)`
- Fillable: user_id, name, description, last_completed_date, frequency_days
- Casts: last_completed_date (date)
- **Key Methods**:
  - `isOverdue()`: Returns boolean if activity is overdue based on frequency
  - `getDaysOverdueAttribute()`: Returns integer of days overdue (0 if not overdue)

## Controllers

### TodoController
**Key Features**:
- **Automatic Daily Generation**: On page load, checks if daily todos need generation
- **Smart Caching**: Uses Laravel cache to prevent duplicate generation per user per day
- **Date-Based Logic**: Generates todos from active recurring todos for current day
- **Activity Reminder Integration**: Shows overdue activities on today's page

**Important Methods**:
- `index()`: Main page - handles auto-generation, displays today's todos and overdue activities
- `generateDailyTodosIfNeeded()`: Private method that handles the daily reset logic
- `completeActivityReminder()`: Marks an activity as completed from the todos page
- Standard CRUD: `store()`, `update()`, `destroy()`

**Auto-Generation Logic**:
1. Check cache key `daily_todos_generated_user_{user_id}`
2. If not generated today, find recurring todos for current day of week
3. Create daily todos from active recurring todos (avoid duplicates)
4. Cache the generation date until end of day

### RecurringTodoController
**Key Features**:
- **Multi-Day Creation**: Single form can create recurring todos for multiple days
- **Day-wise Display**: Groups recurring todos by day of week
- Standard CRUD with authorization policies

**Important Methods**:
- `store()`: Handles array of selected days, creates separate records for each day
- Validation: Ensures at least one day is selected, validates each day

### ActivityTrackerController
**Key Features**:
- **Smart Sorting**: Activities ordered by next due date (soonest first)
- **Due Date Calculation**: Calculates when each activity is next due based on frequency
- **Inline Editing**: AJAX-powered inline editing for name and description
- **Real-time Updates**: Date and frequency updates via AJAX

**Important Methods**:
- `index()`: Displays activities sorted by due date with calculated days until due
- `store()`: Creates new activity tracker with validation
- `update()`: Handles partial updates (AJAX) for inline editing, date, and frequency
- `destroy()`: Deletes activity tracker with authorization check

**Due Date Logic**:
- Calculates next due date as `last_completed_date + frequency_days`
- Sorts activities by `days_until_due` (negative values = overdue, null = no frequency set)
- Activities without frequency or completion date appear last

## Routes Structure

```php
// Authentication routes (from Breeze)
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Todos (daily)
    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::post('/todos/complete-activity-reminder/{activityTracker}', [TodoController::class, 'completeActivityReminder'])->name('todos.complete-activity-reminder');

    // Recurring Todos
    Route::get('/recurring-todos', [RecurringTodoController::class, 'index'])->name('recurring-todos.index');
    Route::post('/recurring-todos', [RecurringTodoController::class, 'store'])->name('recurring-todos.store');
    Route::put('/recurring-todos/{recurringTodo}', [RecurringTodoController::class, 'update'])->name('recurring-todos.update');
    Route::delete('/recurring-todos/{recurringTodo}', [RecurringTodoController::class, 'destroy'])->name('recurring-todos.destroy');

    // Activity Tracker
    Route::get('/activity-trackers', [ActivityTrackerController::class, 'index'])->name('activity-trackers.index');
    Route::post('/activity-trackers', [ActivityTrackerController::class, 'store'])->name('activity-trackers.store');
    Route::put('/activity-trackers/{activityTracker}', [ActivityTrackerController::class, 'update'])->name('activity-trackers.update');
    Route::delete('/activity-trackers/{activityTracker}', [ActivityTrackerController::class, 'destroy'])->name('activity-trackers.destroy');

    // Profile routes (from Breeze)
    // ...
});

// Redirects
Route::get('/', redirect to todos.index)
Route::get('/dashboard', redirect to todos.index)
```

## Views and Frontend

### Layout
- Uses Laravel Breeze's `x-app-layout`
- Navigation includes "Today's Todos", "Recurring Todos", and "Activity Tracker"
- Responsive design with Tailwind CSS

### Today's Todos Page (`resources/views/todos/index.blade.php`)
**Layout Design**:
- **Top**: "Today's Tasks" header with link to recurring todos
- **Activity Reminders**: Overdue activity reminders section (if any exist)
- **Middle**: List of actual todos with checkboxes and delete buttons
- **Bottom**: Add new todo form styled like a todo item with grayed-out radio button

**Activity Reminders Section**:
- Orange-themed design for overdue activities
- Shows activity name, description, last completed date, and days overdue
- One-click completion button to update last completed date
- "Manage" link to activity tracker page

**Todo Item Design**:
- Rounded border containers
- Functional radio buttons (toggleable)
- Green background when completed
- Delete button (trash icon)
- Shows "From recurring todo: Monday" for generated todos

**Add Form Design**:
- Styled like a todo item (same border, spacing)
- Gray background to differentiate from actual todos
- Non-functional grayed-out radio button for visual consistency
- Inline form fields with placeholder text

### Recurring Todos Page (`resources/views/recurring-todos/index.blade.php`)
**Layout Design**:
- **Top**: "Back to Today's Todos" link
- **Two-column layout**:
  - **Left (60% width)**: "Recurring Todos by Day" - organized by day of week
  - **Right (40% width)**: "Add New Recurring Todo" form
- Proper spacing between columns using margin classes

**Multi-Day Form**:
- Title and description inputs
- **Days selector**: 2-column grid of checkboxes (not dropdown)
- Can select multiple days to create multiple recurring todos
- Form validation ensures at least one day is selected

**Recurring Todo Display**:
- Grouped by day of week (Monday through Sunday)
- Each item shows title, description
- Activate/Deactivate and Delete buttons
- Light gray background for individual items

### Activity Tracker Page (`resources/views/activity-trackers/index.blade.php`)
**Layout Design**:
- **Top**: "Back to Today's Todos" link and success messages
- **Two-column layout**:
  - **Left (66% width)**: Activity list sorted by due date
  - **Right (33% width)**: "Add New Activity" form
- Activities ordered by urgency (overdue first, then soonest due)

**Activity Item Design**:
- **Left section**: Date controls and frequency settings
  - Last completed date picker with "time ago" display
  - Inline "Remind every X days" with compact input
- **Right section**: Activity details and due status
  - Click-to-edit name and description (AJAX powered)
  - Due status badges with color coding:
    - **Red**: Overdue activities
    - **Yellow**: Due today
    - **Orange**: Due within 3 days
    - **Green**: Due in 4+ days
  - Delete button (trash icon)

**Interactive Features**:
- **Inline Editing**: Click name/description to edit in place
- **AJAX Updates**: Real-time updates for dates, frequency, and content
- **Smart Sorting**: Automatically orders by next due date
- **Visual Feedback**: Color-coded due status badges

**Add Activity Form**:
- Activity name (required) and description (optional)
- Last completed date and frequency in days
- Validation for required fields and frequency range (1-3650 days)

## Authorization

### Policies
- `TodoPolicy`: Ensures users can only update/delete their own todos
- `RecurringTodoPolicy`: Ensures users can only update/delete their own recurring todos
- `ActivityTrackerPolicy`: Ensures users can only update/delete their own activity trackers
- All use simple `$user->id === $model->user_id` checks

### Middleware
- All routes (todos, recurring todos, activity trackers) protected by `auth` middleware
- No additional permissions needed - user isolation through policies

## Key Features and Behavior

### Daily Reset Philosophy
- **No carryover**: Previous day's todos are not shown
- **Fresh start**: Each day shows only today's todos + newly generated recurring ones
- **Automatic generation**: Happens when user visits today's page (no cron needed)

### Recurring Todo System
- **Day-based**: Recurring todos tied to specific days of week
- **Multi-day creation**: One form submission can create todos for multiple days
- **Auto-generation**: Active recurring todos become daily todos automatically
- **Independent management**: Can activate/deactivate without affecting generated todos

### Activity Tracker System
- **Frequency-based reminders**: Set custom reminder intervals (1-3650 days)
- **Smart due date calculation**: Automatically calculates when activities are next due
- **Priority sorting**: Activities ordered by urgency (overdue → due soon → future)
- **Visual status indicators**: Color-coded badges show due status at a glance
- **Integrated reminders**: Overdue activities appear on daily todos page
- **Flexible completion tracking**: Update last completed date anytime
- **AJAX-powered interface**: Real-time updates without page refresh

### User Experience
- **Intuitive navigation**: Clear separation between daily, recurring, and activity management
- **Visual consistency**: Forms styled like todo items for familiarity
- **Responsive design**: Works on mobile and desktop
- **Immediate feedback**: Success messages, validation errors, and real-time updates
- **Cross-feature integration**: Activity reminders seamlessly appear in daily view

## Development Environment

### Commands Used
- `php artisan serve --port=8087` - Development server
- `npm run dev` - Asset compilation with hot reload (runs on separate port)
- `npm run build` - Production build
- `php artisan migrate` - Database migrations
- `php artisan todos:generate-daily` - Manual todo generation (not needed in normal use)

### Environment Configuration
```
APP_NAME="Todo List"
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/to/database/database.sqlite
```

### Tailwind CSS Notes
- **Version**: 3.1.0+ (via Laravel Breeze)
- **Build process**: Vite compiles CSS, includes only used classes
- **Class availability**: Some margin classes like `mr-6` may not be included until used and compiled
- **Development**: `npm run dev` for hot reload, `npm run build` for production

## Artisan Commands (Available but Not Required)

### Custom Commands Created
- `todos:generate-daily {--date=}` - Manually generate daily todos
- `todos:cleanup-old {--days=7}` - Remove old todos
- **Note**: These are not needed in normal operation due to auto-generation in controller

### Scheduling (Optional)
- Commands can be scheduled in `routes/console.php`
- Not required since auto-generation handles daily todos
- Could be useful for cleanup if desired

## Common Issues and Solutions

### Tailwind CSS Classes Not Working
- **Problem**: Classes like `mr-6` don't apply
- **Solution**: Run `npm run build` or `npm run dev` to compile CSS
- **Why**: Tailwind only includes classes that are actually used in templates

### Database Issues
- **SQLite location**: Must be absolute path in `.env`
- **Permissions**: Ensure web server can read/write to database file
- **Migration**: Run `php artisan migrate` after any schema changes

### Authentication
- **Setup**: Laravel Breeze handles all auth routes and views
- **Customization**: Auth views in `resources/views/auth/`
- **Middleware**: All app routes protected by `auth` middleware

## Future Enhancement Ideas

### Possible Features
- **Time-based todos**: Add time fields for scheduling
- **Categories/Tags**: Group todos by category
- **Statistics**: Track completion rates over time
- **Bulk operations**: Complete/delete multiple todos at once
- **Templates**: Save todo templates for quick creation
- **Export**: Export todos to calendar or other formats
- **Activity insights**: Track completion streaks and patterns for activities
- **Smart notifications**: Email/SMS reminders for overdue activities
- **Activity templates**: Predefined activity sets (e.g., "Home Maintenance")

### Technical Improvements
- **API**: Add REST API for mobile apps
- **Real-time**: WebSocket updates for multiple devices
- **Caching**: Cache recurring todos to reduce database queries
- **Background jobs**: Move cleanup to background queue
- **Testing**: Add comprehensive test suite

## Important Implementation Notes

### Why Controller Auto-Generation Instead of Cron
- **Reliability**: No dependency on server cron setup
- **User-triggered**: Generation happens when user actually needs it
- **Simpler deployment**: No additional server configuration required
- **Immediate results**: User sees generated todos right away

### Cache Strategy
- **Per-user caching**: Each user gets independent generation tracking
- **Daily expiration**: Cache expires at end of day for fresh generation
- **Prevention**: Avoids duplicate generation on page refresh

### Multi-Day Recurring Todos
- **Separate records**: Each day gets its own RecurringTodo record
- **Consistency**: Same title/description across all days
- **Flexibility**: Can deactivate specific days later
- **Simplicity**: Easier to manage than complex day arrays

### Activity Tracker Implementation
- **Controller-level sorting**: Due date calculation and sorting happens in controller for performance
- **Smart due date logic**: Uses Carbon's `addDays()` and `diffInDays()` for accurate calculations
- **Flexible frequency**: Supports any interval from 1-3650 days (10 years)
- **AJAX integration**: Inline editing reduces page refreshes for better UX
- **Cross-feature integration**: Overdue activities automatically appear in daily todos view
- **Memory-efficient**: Activities without frequency settings sorted to end without complex queries

This documentation should provide comprehensive context for future development and maintenance of the Laravel Todo List application.