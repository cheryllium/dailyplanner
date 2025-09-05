#!/bin/bash
cd /home/cheshire/todolist/todolist

if [ -f .dev-pids ]; then
    echo "Stopping development servers..."
    PIDS=$(cat .dev-pids)
    kill $PIDS 2>/dev/null
    rm .dev-pids
    echo "Development servers stopped (PIDs: $PIDS)"
else
    echo "No PID file found, killing by port..."
    # Kill Laravel server (port 8087)
    LARAVEL_PID=$(lsof -ti:8087 2>/dev/null)
    if [ ! -z "$LARAVEL_PID" ]; then
        kill $LARAVEL_PID
        echo "Killed Laravel server (PID: $LARAVEL_PID)"
    fi
    
    # Kill Vite servers (ports 5173, 5174)
    VITE_PID_5173=$(lsof -ti:5173 2>/dev/null)
    VITE_PID_5174=$(lsof -ti:5174 2>/dev/null)
    
    if [ ! -z "$VITE_PID_5173" ]; then
        kill $VITE_PID_5173
        echo "Killed Vite server on port 5173 (PID: $VITE_PID_5173)"
    fi
    
    if [ ! -z "$VITE_PID_5174" ]; then
        kill $VITE_PID_5174
        echo "Killed Vite server on port 5174 (PID: $VITE_PID_5174)"
    fi
    
    if [ -z "$LARAVEL_PID" ] && [ -z "$VITE_PID_5173" ] && [ -z "$VITE_PID_5174" ]; then
        echo "No development servers found running."
    fi
fi