<?php
    $db = parse_url(getenv('CLEARDB_DATABASE_URL'));

    $container->setParameter('database_host', $db['host']);
    $container->setParameter('database_port', $db['port']);
    $container->setParameter('database_name', substr($db["path"], 1));
    $container->setParameter('database_user', $db['user']);
    $container->setParameter('database_password', $db['pass']);
    $container->setParameter('database_host2', $db['host2']);
    $container->setParameter('database_port2', $db['port2']);
    $container->setParameter('database_name2', substr($db["path2"], 1));
    $container->setParameter('database_user2', $db['user2']);
    $container->setParameter('database_password2', $db['pass2']);
    $container->setParameter('secret', getenv('SECRET'));
    $container->setParameter('locale', 'en');
    $container->setParameter('mailer_transport', null);
    $container->setParameter('mailer_host', null);
    $container->setParameter('mailer_user', null);
    $container->setParameter('mailer_password', null);