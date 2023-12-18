<?php

require_once __DIR__ . "/vendor/autoload.php";

use App\Databases\MysqlConnection;
use App\Databases\OrdersListImpl;
use App\Orders\AddItemToOrder;
use App\Orders\AddItemToOrderData;
use App\Orders\CreateOrder;
use App\Orders\CreateOrderData;
use App\Orders\FindOrderById;
use App\Orders\OrderItemData;
use App\Orders\Order;
use App\Orders\OrderAlreadyExistsError;
use App\Orders\OrderNotFoundError;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

if (file_exists(__DIR__ . "/.env")) {
    $_ENV = (Dotenv\Dotenv::createImmutable(__DIR__))
        ->load();
}

$application = new Application(name: "Kata SOLID principles", version: "1.0.0");

$application->register("create-order")
    ->addArgument("id", InputArgument::REQUIRED, "Must be an unsigned integer.")
    ->addOption(
        "value-added-tax",
        "vat",
        InputOption::VALUE_OPTIONAL,
        "Must be between 0.00 and 1.00.",
        Order::DEFAULT_VAT_RATE
    )
    ->setDescription("Create a new order with an ID and a VAT rate.")
    ->setCode(function (InputInterface $input, OutputInterface $output): int {
        $orderId = $input->getArgument("id");
        $orderVatRate = $input->getOption("value-added-tax");
        $useCase = new CreateOrder(new OrdersListImpl(MysqlConnection::createFromEnvVars()));

        try {
            $useCase->execute(new CreateOrderData(id: $orderId, vat_rate: $orderVatRate));
        } catch (OrderAlreadyExistsError $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln("Successfully created order with ID '{$orderId}'.");

        return Command::SUCCESS;
    });

$application->register("display-order")
    ->addArgument("id", InputArgument::REQUIRED, "Must be an unsigned integer.")
    ->setDescription("Display an order by ID.")
    ->setCode(function (InputInterface $input, OutputInterface $output): int {
        $useCase = new FindOrderById(new OrdersListImpl(MysqlConnection::createFromEnvVars()));

        try {
            $order = $useCase->execute($input->getArgument("id"));
        } catch (OrderNotFoundError $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln($order);

        return Command::SUCCESS;
    });

$application->register("add-item-to-order")
    ->addArgument("id", InputArgument::REQUIRED, "Must be an unsigned integer.")
    ->addOption(
        "item-name",
        null,
        InputOption::VALUE_REQUIRED,
        "Must be a string."
    )
    ->addOption(
        "item-price",
        null,
        InputOption::VALUE_REQUIRED,
        "Must be a float."
    )
    ->addOption(
        "item-quantity",
        null,
        InputOption::VALUE_REQUIRED,
        "Must be an integer."
    )
    ->setDescription("Add item to order by ID.")
    ->setCode(function (InputInterface $input, OutputInterface $output): int {
        $ordersList = new OrdersListImpl(MysqlConnection::createFromEnvVars());

        try {
            $order = (new FindOrderById($ordersList))->execute($input->getArgument("id"));
        } catch (OrderNotFoundError $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $useCase = new AddItemToOrder($ordersList);
        $useCase->execute(
            new AddItemToOrderData(
                order: $order,
                item: new OrderItemData(
                    name: $input->getOption("item-name"),
                    price: (float)$input->getOption("item-price"),
                    quantity: (int)$input->getOption("item-quantity"),
                )
            )
        );

        $output->writeln(
            "Item '{$input->getOption("item-name")}' successfully added to order '{$input->getArgument("id")}'."
        );

        return Command::SUCCESS;
    });

$application->run();