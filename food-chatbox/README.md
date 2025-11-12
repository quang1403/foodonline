# Food Chatbox

## Overview
The Food Chatbox is an interactive chat application designed to assist users with food-related inquiries, including recipe suggestions, restaurant recommendations, and food ordering. It leverages AI technology to provide intelligent responses and enhance user experience.

## Project Structure
The project is organized into the following directories and files:

- **src/**: Contains the main application code.
  - **controllers/**: Includes the `FoodChatController.php` which handles incoming messages and manages chat sessions.
  - **models/**: Contains the `FoodChatModel.php` for database interactions related to chat conversations and food data.
  - **config/**: Holds the `openai_config.php` for OpenAI API configuration.
  - **api/**: Includes `chat.php`, the API endpoint for processing chat requests.
  - **assets/**: Contains CSS and JavaScript files for the chatbox interface.
    - **css/**: Styles for the chatbox UI (`foodchatbox.css`).
    - **js/**: JavaScript functionality for the chatbox (`foodchatbox.js`).
  - **database/**: SQL schema file (`food_chat_schema.sql`) for setting up the database.

- **public/**: The entry point for the application.
  - **index.html**: The main HTML file that initializes the chatbox.

- **composer.json**: PHP dependencies configuration file.

- **package.json**: JavaScript dependencies and scripts configuration file.

## Installation
1. Clone the repository:
   ```
   git clone <repository-url>
   cd food-chatbox
   ```

2. Install PHP dependencies using Composer:
   ```
   composer install
   ```

3. Install JavaScript dependencies using npm:
   ```
   npm install
   ```

4. Set up the database:
   - Import the `food_chat_schema.sql` file into your database.

## Usage
- Open `public/index.html` in your web browser to access the chatbox.
- Start interacting with the chatbox by asking food-related questions or requesting assistance with food orders.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.