<?php

namespace Eccube\ServiceProvider;

use Pimple\Container;
use Eccube\Application;
use Silex\Application as BaseApplication;
use Pimple\ServiceProviderInterface;

class EccubeServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        // Service
        $app['eccube.service.system'] = function () use ($app) {
            return new \Eccube\Service\SystemService($app);
        };

        $app['eccube.service.csv.export'] = function () use ($app) {
            $csvService = new \Eccube\Service\CsvExportService();
            $csvService->setEntityManager($app['orm.em']);
            $csvService->setConfig($app['config']);
            $csvService->setCsvRepository($app['eccube.repository.csv']);
            $csvService->setCsvTypeRepository($app['eccube.repository.master.csv_type']);
            $csvService->setOrderRepository($app['eccube.repository.order']);
            $csvService->setCustomerRepository($app['eccube.repository.customer']);
            $csvService->setProductRepository($app['eccube.repository.product']);

            return $csvService;
        };

        // Form\Type
        $app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\FreezeTypeExtension();

            return $extensions;
        });
    }

}
