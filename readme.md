<h1>About PayPal Laravel E-commerce application</h1>

This application will demonstrate how to utilise the PayPal Checkout API in a Laravel application by creating a simple web store where users can purchase products.

<h3>Steps to get the Laravel Application working</h3>

You must have a local server (e.g. WAMP) running and properly configured virtual host.

<ol>
    <li>Clone the project: https://github.com/hsrahman/PayPalLaravelApp.git</li>
    <li>Within the root directory run the command: composer install</li>
    <li>Run the command: NPM install</li>
    <li>Run the command: cp .env.example .env</li>
    <li>Run the command: php artisan key:generate</li>
    <li>Create a database</li>
    <li>Update .env file with the database credentials</li>
    <li>Run php migration command: php artisan migrate</li>
    <li>Finally add some fake products and create a new user</li>
</ol>

The PayPal API is not configured yet and further instructions on how to get this working can be found in the tutorial: <a href="https://project-hn.000webhostapp.com/article/25" >Beginners guide to make an e-commerce store using PayPal Checkout in Laravel</a>.

Visit my website for more awesome tutorials at <a href="https://project-hn.000webhostapp.com/articles" >Project HN</a>
