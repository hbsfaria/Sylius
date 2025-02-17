<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Webmozart\Assert\Assert;

final class ShipmentContext implements Context
{
    private ApiClientInterface $client;

    private ResponseCheckerInterface $responseChecker;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I try to see the shipment of the order placed by a customer :customer
     */
    public function iTryToSeeTheShipmentOfTheOrderPlacedByACustomer(CustomerInterface $customer): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        Assert::eq($order->getCustomer(), $customer);

        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();

        $this->client->show('shipments', (string) $shipment->getId());
    }

    /**
     * @Then I should not be able to see that shipment
     */
    public function iShouldNotBeAbleToSeeThatShipment(): void
    {
        Assert::false($this->responseChecker->isShowSuccessful($this->client->getLastResponse()));
    }
}
