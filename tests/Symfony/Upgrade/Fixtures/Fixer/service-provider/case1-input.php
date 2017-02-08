<?php

namespace Eccube\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class EccubeServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // Service
        $app['eccube.service.system'] = $app->share(function () use ($app) {
            return new \Eccube\Service\SystemService($app);
        });

        $app['eccube.service.csv.export'] = $app->share(function () use ($app) {
            $csvService = new \Eccube\Service\CsvExportService();
            $csvService->setEntityManager($app['orm.em']);
            $csvService->setConfig($app['config']);
            $csvService->setCsvRepository($app['eccube.repository.csv']);
            $csvService->setCsvTypeRepository($app['eccube.repository.master.csv_type']);
            $csvService->setOrderRepository($app['eccube.repository.order']);
            $csvService->setCustomerRepository($app['eccube.repository.customer']);
            $csvService->setProductRepository($app['eccube.repository.product']);

            return $csvService;
        });

        // Form\Type
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\FreezeTypeExtension();

            return $extensions;
        }));
    }

}
