## Setup Instructions

Follow these steps to get the project running locally:

1. **Clone the repository**
   ```bash
   git clone https://github.com/Gajaluxsan/smart-ticket.git
   cd project-name
   composer install
   npm i
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run dev || build
   php artisan serve

2. ** Assumptions & Trade-offs **
    . Add user authentication to the tracking system for security.
    . List AI log responses to users, so usage details can be easily managed.
    . Maintain ticket tracking workflow to easily handle who is working on what.
    When creating a ticket, show a button to generate an explanation, and display the explanation to the ticket creator. The user can easily check it or regenerate it.
    . Write another cron job to regenerate tickets with low confidence.
    . Use real-time usage with WebSockets for AI suggestions in the ticket body.
    . Implement a more attractive and user-friendly UI.