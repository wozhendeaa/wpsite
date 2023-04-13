<?php return array(
    'root' => array(
        'name' => 'wprss/core',
        'pretty_version' => 'dev-develop',
        'version' => 'dev-develop',
        'reference' => '2bed06478cf8bc3021528c7936e68a8e4aeced82',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'container-interop/container-interop' => array(
            'pretty_version' => '1.2.0',
            'version' => '1.2.0.0',
            'reference' => '79cbf1341c22ec75643d841642dd5d6acd83bdb8',
            'type' => 'library',
            'install_path' => __DIR__ . '/../container-interop/container-interop',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'container-interop/container-interop-implementation' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '^1.0',
            ),
        ),
        'container-interop/service-provider' => array(
            'pretty_version' => 'v0.3.0',
            'version' => '0.3.0.0',
            'reference' => '5cb38893b836edb00d3e1ace26c20ee1d29957cf',
            'type' => 'library',
            'install_path' => __DIR__ . '/../container-interop/service-provider',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/collections-abstract' => array(
            'pretty_version' => 'v0.1.0',
            'version' => '0.1.0.0',
            'reference' => '7c9141202af4d83b31e75efcbf481fa1cb588ad8',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/collections-abstract',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/collections-abstract-base' => array(
            'pretty_version' => 'v0.1.0',
            'version' => '0.1.0.0',
            'reference' => '0ec0147451a72a13fb327ac6ff747c5e953c56f3',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/collections-abstract-base',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/collections-interface' => array(
            'pretty_version' => 'v0.1.2',
            'version' => '0.1.2.0',
            'reference' => 'a8e9a30366a30d77bc270042a67150b3f8f6d94d',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/collections-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/container-helper-base' => array(
            'pretty_version' => 'v0.1-alpha8',
            'version' => '0.1.0.0-alpha8',
            'reference' => '1b8206842e06b71abcff769aaddfc51bf8f317cd',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/container-helper-base',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/di' => array(
            'pretty_version' => 'v0.1.1',
            'version' => '0.1.1.0',
            'reference' => '52efd50f1b11fcdd2f789bae483bdb858772c306',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/di',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/di-abstract' => array(
            'pretty_version' => 'v0.1',
            'version' => '0.1.0.0',
            'reference' => 'e87ee3782d5f4c44724e79c4b2e1a55103e5cd11',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/di-abstract',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/di-interface' => array(
            'pretty_version' => 'v0.1',
            'version' => '0.1.0.0',
            'reference' => '0320846a577d68b761e29acd5d4db40d88cc4f98',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/di-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/exception' => array(
            'pretty_version' => 'v0.1-alpha5',
            'version' => '0.1.0.0-alpha5',
            'reference' => 'f7afb934c970a4e167b2c7ba24fa3df50714e0fe',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/exception',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/exception-helper-base' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '0.1-alpha1|0.1-alpha2',
            ),
        ),
        'dhii/exception-interface' => array(
            'pretty_version' => 'v0.2',
            'version' => '0.2.0.0',
            'reference' => 'b69feebf7cb2879cd43977a03342e2393b73f7fb',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/exception-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/factory-interface' => array(
            'pretty_version' => 'v0.1',
            'version' => '0.1.0.0',
            'reference' => 'b8d217aec8838e64ccaa770cb03dc164bf6f0515',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/factory-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/i18n-helper-base' => array(
            'pretty_version' => 'v0.1-alpha1',
            'version' => '0.1.0.0-alpha1',
            'reference' => 'fc4c881f3e528ea918588831ebeffb92738f8dd5',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/i18n-helper-base',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/i18n-interface' => array(
            'pretty_version' => 'v0.2',
            'version' => '0.2.0.0',
            'reference' => '7eaf0731ba80eea37a5deea6d894a0326e10be67',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/i18n-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/iterator-helper-base' => array(
            'pretty_version' => 'v0.1-alpha2',
            'version' => '0.1.0.0-alpha2',
            'reference' => 'cf62fb9f8b658a82815f15a2d906d8d1ff5c52ce',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/iterator-helper-base',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/normalization-helper-base' => array(
            'pretty_version' => 'v0.1-alpha4',
            'version' => '0.1.0.0-alpha4',
            'reference' => '1b64f0ea6b3e32f9478f854f6049500795b51da7',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/normalization-helper-base',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/output-renderer-abstract' => array(
            'pretty_version' => 'v0.1-alpha2',
            'version' => '0.1.0.0-alpha2',
            'reference' => '0f6e5eed940025332dd1986d6c771e10f7197b1a',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/output-renderer-abstract',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/output-renderer-base' => array(
            'pretty_version' => 'v0.1-alpha1',
            'version' => '0.1.0.0-alpha1',
            'reference' => '700483a37016e502be2ead9580bb9258ad8bf17b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/output-renderer-base',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/output-renderer-interface' => array(
            'pretty_version' => 'v0.3',
            'version' => '0.3.0.0',
            'reference' => '407014d7fd1af0427958f2acd61aff008ee9e032',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/output-renderer-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/stats-abstract' => array(
            'pretty_version' => 'v0.1.0',
            'version' => '0.1.0.0',
            'reference' => '71f6702c3257c71ab3917b6d076d3c3588cc9f49',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/stats-abstract',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/stats-interface' => array(
            'pretty_version' => 'v0.1.0',
            'version' => '0.1.0.0',
            'reference' => '36d09a8b8a3b8058214dae6eefb8ecdd98e0f0ba',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/stats-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/stringable-interface' => array(
            'pretty_version' => 'v0.1',
            'version' => '0.1.0.0',
            'reference' => 'b6653905eef2ebf377749feb80a6d18abbe913ef',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/stringable-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/transformer-interface' => array(
            'pretty_version' => 'v0.1-alpha1',
            'version' => '0.1.0.0-alpha1',
            'reference' => 'e774efef46413eb34bdafc19a6bd74fbf656235d',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/transformer-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/validation-abstract' => array(
            'pretty_version' => 'v0.2-alpha1',
            'version' => '0.2.0.0-alpha1',
            'reference' => 'dff998ba3476927a0fc6d10bb022425de8f1c844',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/validation-abstract',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/validation-base' => array(
            'pretty_version' => 'v0.2-alpha2',
            'version' => '0.2.0.0-alpha2',
            'reference' => '9e75c5f886a2403c6989c36c2d4ffcfae158172e',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/validation-base',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'dhii/validation-interface' => array(
            'pretty_version' => 'v0.2',
            'version' => '0.2.0.0',
            'reference' => 'a26026ae5cdf0a650b3511a22dbcb9329073b82c',
            'type' => 'library',
            'install_path' => __DIR__ . '/../dhii/validation-interface',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'erusev/parsedown' => array(
            'pretty_version' => '1.7.4',
            'version' => '1.7.4.0',
            'reference' => 'cb17b6477dfff935958ba01325f2e8a2bfa6dab3',
            'type' => 'library',
            'install_path' => __DIR__ . '/../erusev/parsedown',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/container' => array(
            'pretty_version' => '1.0.0',
            'version' => '1.0.0.0',
            'reference' => 'b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/container',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/log' => array(
            'pretty_version' => '1.1.4',
            'version' => '1.1.4.0',
            'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/log',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'rebelcode/composer-cleanup-plugin' => array(
            'pretty_version' => 'v0.2',
            'version' => '0.2.0.0',
            'reference' => '3677eb752eb8ca042ba6f725b33f419b93857a62',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/../rebelcode/composer-cleanup-plugin',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'reference' => 'aed596913b70fae57be53d86faa2e9ef85a2297b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-ctype',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'reference' => 'b5f7b932ee6fa802fc792eabd77c4c88084517ce',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-mbstring',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'symfony/translation' => array(
            'pretty_version' => 'v2.8.52',
            'version' => '2.8.52.0',
            'reference' => 'fc58c2a19e56c29f5ba2736ec40d0119a0de2089',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/translation',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'twig/extensions' => array(
            'pretty_version' => 'v1.5.4',
            'version' => '1.5.4.0',
            'reference' => '57873c8b0c1be51caa47df2cdb824490beb16202',
            'type' => 'library',
            'install_path' => __DIR__ . '/../twig/extensions',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'twig/twig' => array(
            'pretty_version' => 'v1.44.6',
            'version' => '1.44.6.0',
            'reference' => 'ae39480f010ef88adc7938503c9b02d3baf2f3b3',
            'type' => 'library',
            'install_path' => __DIR__ . '/../twig/twig',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'wprss/core' => array(
            'pretty_version' => 'dev-develop',
            'version' => 'dev-develop',
            'reference' => '2bed06478cf8bc3021528c7936e68a8e4aeced82',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);