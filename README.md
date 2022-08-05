# To whom is this for?

This is meant to be used by developers of Mpire Labs, led by Adrian Morrison.

# What is this for?

Is it highly useful when starting a project aiming the Shopify API environment. This is a batteries included boilerplate for Shopify.

It includes:

1. Webhook Authentication
2. HMAC + OAuth2 Authentication
3. Automatic Shopify Store and license authentication through the `AccessAdrian.com` portal
4. Automatic installation of your webhooks
5. Automatic request for claims for the OAuth2 token

# How to use it

This is a typical Laravel 5.x app. (Currently at `v5.4.28` at time of writing this)

This means you can use the provided controllers and configuration file(s) to extend the default behaviour.


# How to configure it

See [config/shopify.php](config/shopify.php)


# Things that were done for you

The `public` storage was already linked (`artisan storage:link`).

A Webhook (`app/uninstalled`) was already created for you. To hook into it, either modify the controller, or create an event (https://laravel.com/docs/5.4/events). **The webhook will remove the shopify store from the database. The event is triggered because any database changes**

The `ShopifyStore` model inherits `Authenticatable`. Use it with Gates and Policies to restrict access to them.

# Things you need to think about

When you add models, make sure you add a `onDelete 'cascade'` in your migrations. This will allow the database to clean itself. This means much, much less maintenance long term.

We **highly** encourage you to use Gates and Policies.


# By whom was this made?

By Felix Lebel