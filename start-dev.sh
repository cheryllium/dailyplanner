#!/bin/bash
cd /home/cheshire/todolist/todolist

# Start Laravel server in background
php artisan serve --host=192.168.0.124 --port=8087 &
LARAVEL_PID=$!

# Start Vite in background  
npm run dev &
VITE_PID=$!

echo "Laravel server started (PID: $LARAVEL_PID) - http://192.168.0.124:8087"
echo "Vite dev server started (PID: $VITE_PID)"
echo "To stop: kill $LARAVEL_PID $VITE_PID"

# Save PIDs to file for easy killing later
echo "$LARAVEL_PID $VITE_PID" > .dev-pids

echo "Development servers are running in the background."
echo "Run ./stop-dev.sh to stop both servers."