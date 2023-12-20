<?php

require_once __DIR__ . "/vendor/autoload.php";

use App\Bootstrap\App;
use App\Domain\Orders\AddItemToOrderData;
use App\Domain\Orders\CreateOrderData;
use App\Domain\Orders\Order;
use App\Domain\Orders\OrderAlreadyExistsError;
use App\Domain\Orders\OrderItemData;
use App\Domain\Orders\OrderNotFoundError;
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

$app = App::createFromEnvVars();

$cli = new Application(name: "Kata SOLID principles", version: "1.0.0");

$cli->register("create-order")
    ->addArgument("id", InputArgument::REQUIRED, "Must be an unsigned integer.")
    ->addOption(
        "value-added-tax",
        "vat",
        InputOption::VALUE_OPTIONAL,
        "Must be between 0.00 and 1.00.",
        Order::DEFAULT_VAT_RATE
    )
    ->setDescription("Create a new order with an ID and a VAT rate.")
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app): int {
        try {
            $app->orders()->createNew(
                new CreateOrderData(
                    id: $input->getArgument("id"),
                    vat_rate: $input->getOption("value-added-tax")
                )
            );
        } catch (OrderAlreadyExistsError $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln("Successfully created order with ID '{$input->getArgument("id")}'.");

        return Command::SUCCESS;
    });

$cli->register("display-order")
    ->addArgument("id", InputArgument::REQUIRED, "Must be an unsigned integer.")
    ->setDescription("Display an order by ID.")
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app): int {
        try {
            $order = $app->orders()->findById($input->getArgument("id"));
        } catch (OrderNotFoundError $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln($order);

        return Command::SUCCESS;
    });

$cli->register("add-item-to-order")
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
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app): int {
        try {
            $order = $app->orders()->findById($input->getArgument("id"));
        } catch (OrderNotFoundError $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $app->orders()->addItemToOrder(
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

$cli->run();