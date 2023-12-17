# Kata SOLID principles & Hexagonal Architecture

Practical exercise applying the concepts from my course on SOLID principles and hexagonal architecture for Master's
students in CTO and Tech Lead roles at HETIC.

## Context

This is a simple PHP application that allows you to create orders, add items to the order, calculate the total by
applying VAT, and view orders.

With this deliberately poorly designed code, where persistence and the command system are tightly coupled, the initial
objective is to encourage students to write unit tests without modifying the source code, in order to demonstrate how
challenging it is to test an application when components are strongly interconnected. In a second phase, the goal is to
ask them to migrate the persistence to a non-relational database without altering the existing code, thus illustrating
the complexity of evolving a poorly-architected application. Finally, in a third phase, the aim is to instruct students
to refactor the code following the SOLID principles and the taught hexagonal architecture, making the code more
understandable, testable, and adaptable.

## Requirements

- PHP ^8.2
- MySql ^8.0

## Get started

### `setup`

- Create a `.env` file like `.env.example` and configure it.
- Import the [database schema](db-schema.sql) into your MySQL database.

### `install`

```sh
php composer.phar install
```

### `usage`

```sh
php cli.php

# Create an order
php cli.php create-order 1

# Display an order
php cli.php display-order 1

# Add an element to an order
php cli.php add-item-to-order 1 --item-name Book --item-quantity 2 --item-price "12.00"
```

## License

Please see [License File](LICENSE) for more information.