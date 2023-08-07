<h1>Laravel Flight</h1>

<p>This is a Laravel Flight project, a web application that helps you manage your flights. Before getting started, make sure you have the following dependencies installed on your computer:</p>

<h2>Prerequisites:</h2>
<ul>
  <li>PHP (version 8.1 or higher)</li>
  <li>Composer (dependency management tool for PHP)</li>
  <li>Git (version control system)</li>
  <li>Node.js installed</li>
</ul>

<h2>Clone the Repository:</h2>
<p>You can clone this repository by running the following command in your terminal:</p>

<code>git clone https://github.com/zebdiriadh/laravel-flight.git</code>

<p>Alternatively, you can download the repository manually from the GitHub page.</p>

<h2>Install Dependencies:</h2>
<p>After cloning the repository, navigate to the project directory using the terminal:</p>

<code>cd laravel-flight</code>

<p>Before proceeding, ensure that your <code>php.ini</code> has the following extensions enabled:</p>
<ul>
  <li><code>extension=zip</code></li>
  <li><code>extension=fileinfo</code></li>
</ul>

<h2>Install Project Dependencies:</h2>
<p>Run the following commands in the terminal to install the project dependencies:</p>

<code>composer install</code><br>
<code>php artisan key:generate</code><br>
<code>npm install</code><br>
<code>npm run dev</code>

<p>These commands will install PHP and JavaScript dependencies required for the application.</p>

<h2>Start the Application:</h2>
<p>After installing the dependencies, you can start the Laravel development server by running the following command:</p>

<code>php artisan serve</code>

<p>The application should now be running locally on your computer. Access it in your web browser by visiting <a href="http://localhost:8000">http://localhost:8000</a>.</p>
