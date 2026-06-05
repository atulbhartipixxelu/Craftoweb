<?php
/* Template Name: Auth Page */
get_header();
?>

<div class="auth-container">
    
    <!-- SIGN UP -->
    <div class="auth-box">
        <h2>Sign Up</h2>
        <form id="signupForm">
            <input type="text" name="first_name" placeholder="First name" required>
            <input type="text" name="last_name" placeholder="Last name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
            <div class="response"></div>
        </form>
    </div>

    <!-- SIGN IN -->
    <div class="auth-box">
        <h2>Sign In</h2>
        <form id="loginForm">
            <input type="text" name="username" placeholder="Email or Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign In</button>
            <div class="response"></div>
        </form>
    </div>

</div>

<?php get_footer(); ?>