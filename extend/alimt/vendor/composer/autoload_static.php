<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit52263655429b3a0d89ff1360d19e9d20
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'd767e4fc2dc52fe66584ab8c6684783e' => __DIR__ . '/..' . '/adbario/php-dot-notation/src/helpers.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        'b067bc7112e384b61c701452d53a14a8' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/JmesPath.php',
        '66453932bc1be9fb2f910a27947d11b6' => __DIR__ . '/..' . '/alibabacloud/client/src/Functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'clagiordano\\weblibs\\configmanager\\' => 34,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Yaml\\' => 23,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
        ),
        'J' => 
        array (
            'JmesPath\\' => 9,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'A' => 
        array (
            'AlibabaCloud\\Client\\' => 20,
            'AlibabaCloud\\Alimt\\' => 19,
            'Adbar\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'clagiordano\\weblibs\\configmanager\\' => 
        array (
            0 => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
            1 => __DIR__ . '/..' . '/psr/http-factory/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'JmesPath\\' => 
        array (
            0 => __DIR__ . '/..' . '/mtdowling/jmespath.php/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'AlibabaCloud\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/alibabacloud/client/src',
        ),
        'AlibabaCloud\\Alimt\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Adbar\\' => 
        array (
            0 => __DIR__ . '/..' . '/adbario/php-dot-notation/src',
        ),
    );

    public static $classMap = array (
        'Adbar\\Dot' => __DIR__ . '/..' . '/adbario/php-dot-notation/src/Dot.php',
        'AlibabaCloud\\Alimt\\Alimt' => __DIR__ . '/../..' . '/Alimt.php',
        'AlibabaCloud\\Alimt\\V20181012\\AlimtApiResolver' => __DIR__ . '/../..' . '/V20181012/AlimtApiResolver.php',
        'AlibabaCloud\\Client\\Accept' => __DIR__ . '/..' . '/alibabacloud/client/src/Accept.php',
        'AlibabaCloud\\Client\\AlibabaCloud' => __DIR__ . '/..' . '/alibabacloud/client/src/AlibabaCloud.php',
        'AlibabaCloud\\Client\\Clients\\AccessKeyClient' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/AccessKeyClient.php',
        'AlibabaCloud\\Client\\Clients\\BearerTokenClient' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/BearerTokenClient.php',
        'AlibabaCloud\\Client\\Clients\\Client' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/Client.php',
        'AlibabaCloud\\Client\\Clients\\EcsRamRoleClient' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/EcsRamRoleClient.php',
        'AlibabaCloud\\Client\\Clients\\ManageTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/ManageTrait.php',
        'AlibabaCloud\\Client\\Clients\\RamRoleArnClient' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/RamRoleArnClient.php',
        'AlibabaCloud\\Client\\Clients\\RsaKeyPairClient' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/RsaKeyPairClient.php',
        'AlibabaCloud\\Client\\Clients\\StsClient' => __DIR__ . '/..' . '/alibabacloud/client/src/Clients/StsClient.php',
        'AlibabaCloud\\Client\\Config\\Config' => __DIR__ . '/..' . '/alibabacloud/client/src/Config/Config.php',
        'AlibabaCloud\\Client\\Credentials\\AccessKeyCredential' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/AccessKeyCredential.php',
        'AlibabaCloud\\Client\\Credentials\\BearerTokenCredential' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/BearerTokenCredential.php',
        'AlibabaCloud\\Client\\Credentials\\CredentialsInterface' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/CredentialsInterface.php',
        'AlibabaCloud\\Client\\Credentials\\EcsRamRoleCredential' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/EcsRamRoleCredential.php',
        'AlibabaCloud\\Client\\Credentials\\Ini\\CreateTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Ini/CreateTrait.php',
        'AlibabaCloud\\Client\\Credentials\\Ini\\IniCredential' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Ini/IniCredential.php',
        'AlibabaCloud\\Client\\Credentials\\Ini\\OptionsTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Ini/OptionsTrait.php',
        'AlibabaCloud\\Client\\Credentials\\Providers\\CredentialsProvider' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Providers/CredentialsProvider.php',
        'AlibabaCloud\\Client\\Credentials\\Providers\\EcsRamRoleProvider' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Providers/EcsRamRoleProvider.php',
        'AlibabaCloud\\Client\\Credentials\\Providers\\Provider' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Providers/Provider.php',
        'AlibabaCloud\\Client\\Credentials\\Providers\\RamRoleArnProvider' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Providers/RamRoleArnProvider.php',
        'AlibabaCloud\\Client\\Credentials\\Providers\\RsaKeyPairProvider' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Providers/RsaKeyPairProvider.php',
        'AlibabaCloud\\Client\\Credentials\\RamRoleArnCredential' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/RamRoleArnCredential.php',
        'AlibabaCloud\\Client\\Credentials\\Requests\\AssumeRole' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Requests/AssumeRole.php',
        'AlibabaCloud\\Client\\Credentials\\Requests\\GenerateSessionAccessKey' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/Requests/GenerateSessionAccessKey.php',
        'AlibabaCloud\\Client\\Credentials\\RsaKeyPairCredential' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/RsaKeyPairCredential.php',
        'AlibabaCloud\\Client\\Credentials\\StsCredential' => __DIR__ . '/..' . '/alibabacloud/client/src/Credentials/StsCredential.php',
        'AlibabaCloud\\Client\\DefaultAcsClient' => __DIR__ . '/..' . '/alibabacloud/client/src/DefaultAcsClient.php',
        'AlibabaCloud\\Client\\Encode' => __DIR__ . '/..' . '/alibabacloud/client/src/Encode.php',
        'AlibabaCloud\\Client\\Exception\\AlibabaCloudException' => __DIR__ . '/..' . '/alibabacloud/client/src/Exception/AlibabaCloudException.php',
        'AlibabaCloud\\Client\\Exception\\ClientException' => __DIR__ . '/..' . '/alibabacloud/client/src/Exception/ClientException.php',
        'AlibabaCloud\\Client\\Exception\\ServerException' => __DIR__ . '/..' . '/alibabacloud/client/src/Exception/ServerException.php',
        'AlibabaCloud\\Client\\Filter\\ApiFilter' => __DIR__ . '/..' . '/alibabacloud/client/src/Filter/ApiFilter.php',
        'AlibabaCloud\\Client\\Filter\\ClientFilter' => __DIR__ . '/..' . '/alibabacloud/client/src/Filter/ClientFilter.php',
        'AlibabaCloud\\Client\\Filter\\CredentialFilter' => __DIR__ . '/..' . '/alibabacloud/client/src/Filter/CredentialFilter.php',
        'AlibabaCloud\\Client\\Filter\\Filter' => __DIR__ . '/..' . '/alibabacloud/client/src/Filter/Filter.php',
        'AlibabaCloud\\Client\\Filter\\HttpFilter' => __DIR__ . '/..' . '/alibabacloud/client/src/Filter/HttpFilter.php',
        'AlibabaCloud\\Client\\Log\\LogFormatter' => __DIR__ . '/..' . '/alibabacloud/client/src/Log/LogFormatter.php',
        'AlibabaCloud\\Client\\Profile\\DefaultProfile' => __DIR__ . '/..' . '/alibabacloud/client/src/Profile/DefaultProfile.php',
        'AlibabaCloud\\Client\\Regions\\EndpointProvider' => __DIR__ . '/..' . '/alibabacloud/client/src/Regions/EndpointProvider.php',
        'AlibabaCloud\\Client\\Regions\\LocationService' => __DIR__ . '/..' . '/alibabacloud/client/src/Regions/LocationService.php',
        'AlibabaCloud\\Client\\Regions\\LocationServiceRequest' => __DIR__ . '/..' . '/alibabacloud/client/src/Regions/LocationServiceRequest.php',
        'AlibabaCloud\\Client\\Release' => __DIR__ . '/..' . '/alibabacloud/client/src/Release.php',
        'AlibabaCloud\\Client\\Request\\Request' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/Request.php',
        'AlibabaCloud\\Client\\Request\\RoaRequest' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/RoaRequest.php',
        'AlibabaCloud\\Client\\Request\\RpcRequest' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/RpcRequest.php',
        'AlibabaCloud\\Client\\Request\\Traits\\AcsTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/Traits/AcsTrait.php',
        'AlibabaCloud\\Client\\Request\\Traits\\ClientTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/Traits/ClientTrait.php',
        'AlibabaCloud\\Client\\Request\\Traits\\DeprecatedRoaTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/Traits/DeprecatedRoaTrait.php',
        'AlibabaCloud\\Client\\Request\\Traits\\DeprecatedTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/Traits/DeprecatedTrait.php',
        'AlibabaCloud\\Client\\Request\\Traits\\RetryTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/Traits/RetryTrait.php',
        'AlibabaCloud\\Client\\Request\\UserAgent' => __DIR__ . '/..' . '/alibabacloud/client/src/Request/UserAgent.php',
        'AlibabaCloud\\Client\\Resolver\\ActionResolverTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Resolver/ActionResolverTrait.php',
        'AlibabaCloud\\Client\\Resolver\\ApiResolver' => __DIR__ . '/..' . '/alibabacloud/client/src/Resolver/ApiResolver.php',
        'AlibabaCloud\\Client\\Resolver\\CallTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Resolver/CallTrait.php',
        'AlibabaCloud\\Client\\Resolver\\Roa' => __DIR__ . '/..' . '/alibabacloud/client/src/Resolver/Roa.php',
        'AlibabaCloud\\Client\\Resolver\\Rpc' => __DIR__ . '/..' . '/alibabacloud/client/src/Resolver/Rpc.php',
        'AlibabaCloud\\Client\\Resolver\\VersionResolver' => __DIR__ . '/..' . '/alibabacloud/client/src/Resolver/VersionResolver.php',
        'AlibabaCloud\\Client\\Result\\Result' => __DIR__ . '/..' . '/alibabacloud/client/src/Result/Result.php',
        'AlibabaCloud\\Client\\SDK' => __DIR__ . '/..' . '/alibabacloud/client/src/SDK.php',
        'AlibabaCloud\\Client\\Signature\\BearerTokenSignature' => __DIR__ . '/..' . '/alibabacloud/client/src/Signature/BearerTokenSignature.php',
        'AlibabaCloud\\Client\\Signature\\ShaHmac1Signature' => __DIR__ . '/..' . '/alibabacloud/client/src/Signature/ShaHmac1Signature.php',
        'AlibabaCloud\\Client\\Signature\\ShaHmac256Signature' => __DIR__ . '/..' . '/alibabacloud/client/src/Signature/ShaHmac256Signature.php',
        'AlibabaCloud\\Client\\Signature\\ShaHmac256WithRsaSignature' => __DIR__ . '/..' . '/alibabacloud/client/src/Signature/ShaHmac256WithRsaSignature.php',
        'AlibabaCloud\\Client\\Signature\\Signature' => __DIR__ . '/..' . '/alibabacloud/client/src/Signature/Signature.php',
        'AlibabaCloud\\Client\\Signature\\SignatureInterface' => __DIR__ . '/..' . '/alibabacloud/client/src/Signature/SignatureInterface.php',
        'AlibabaCloud\\Client\\Support\\Arrays' => __DIR__ . '/..' . '/alibabacloud/client/src/Support/Arrays.php',
        'AlibabaCloud\\Client\\Support\\Path' => __DIR__ . '/..' . '/alibabacloud/client/src/Support/Path.php',
        'AlibabaCloud\\Client\\Support\\Sign' => __DIR__ . '/..' . '/alibabacloud/client/src/Support/Sign.php',
        'AlibabaCloud\\Client\\Support\\Stringy' => __DIR__ . '/..' . '/alibabacloud/client/src/Support/Stringy.php',
        'AlibabaCloud\\Client\\Traits\\ArrayAccessTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/ArrayAccessTrait.php',
        'AlibabaCloud\\Client\\Traits\\ClientTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/ClientTrait.php',
        'AlibabaCloud\\Client\\Traits\\DefaultRegionTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/DefaultRegionTrait.php',
        'AlibabaCloud\\Client\\Traits\\EndpointTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/EndpointTrait.php',
        'AlibabaCloud\\Client\\Traits\\HasDataTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/HasDataTrait.php',
        'AlibabaCloud\\Client\\Traits\\HistoryTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/HistoryTrait.php',
        'AlibabaCloud\\Client\\Traits\\HttpTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/HttpTrait.php',
        'AlibabaCloud\\Client\\Traits\\LogTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/LogTrait.php',
        'AlibabaCloud\\Client\\Traits\\MockTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/MockTrait.php',
        'AlibabaCloud\\Client\\Traits\\ObjectAccessTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/ObjectAccessTrait.php',
        'AlibabaCloud\\Client\\Traits\\RegionTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/RegionTrait.php',
        'AlibabaCloud\\Client\\Traits\\RequestTrait' => __DIR__ . '/..' . '/alibabacloud/client/src/Traits/RequestTrait.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'GuzzleHttp\\BodySummarizer' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/BodySummarizer.php',
        'GuzzleHttp\\BodySummarizerInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/BodySummarizerInterface.php',
        'GuzzleHttp\\Client' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Client.php',
        'GuzzleHttp\\ClientInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/ClientInterface.php',
        'GuzzleHttp\\ClientTrait' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/ClientTrait.php',
        'GuzzleHttp\\Cookie\\CookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/CookieJar.php',
        'GuzzleHttp\\Cookie\\CookieJarInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php',
        'GuzzleHttp\\Cookie\\FileCookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/FileCookieJar.php',
        'GuzzleHttp\\Cookie\\SessionCookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/SessionCookieJar.php',
        'GuzzleHttp\\Cookie\\SetCookie' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/SetCookie.php',
        'GuzzleHttp\\Exception\\BadResponseException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/BadResponseException.php',
        'GuzzleHttp\\Exception\\ClientException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ClientException.php',
        'GuzzleHttp\\Exception\\ConnectException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ConnectException.php',
        'GuzzleHttp\\Exception\\GuzzleException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/GuzzleException.php',
        'GuzzleHttp\\Exception\\InvalidArgumentException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/InvalidArgumentException.php',
        'GuzzleHttp\\Exception\\RequestException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/RequestException.php',
        'GuzzleHttp\\Exception\\ServerException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ServerException.php',
        'GuzzleHttp\\Exception\\TooManyRedirectsException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/TooManyRedirectsException.php',
        'GuzzleHttp\\Exception\\TransferException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/TransferException.php',
        'GuzzleHttp\\HandlerStack' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/HandlerStack.php',
        'GuzzleHttp\\Handler\\CurlFactory' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlFactory.php',
        'GuzzleHttp\\Handler\\CurlFactoryInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php',
        'GuzzleHttp\\Handler\\CurlHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlHandler.php',
        'GuzzleHttp\\Handler\\CurlMultiHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php',
        'GuzzleHttp\\Handler\\EasyHandle' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/EasyHandle.php',
        'GuzzleHttp\\Handler\\HeaderProcessor' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/HeaderProcessor.php',
        'GuzzleHttp\\Handler\\MockHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/MockHandler.php',
        'GuzzleHttp\\Handler\\Proxy' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/Proxy.php',
        'GuzzleHttp\\Handler\\StreamHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/StreamHandler.php',
        'GuzzleHttp\\MessageFormatter' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/MessageFormatter.php',
        'GuzzleHttp\\MessageFormatterInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/MessageFormatterInterface.php',
        'GuzzleHttp\\Middleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Middleware.php',
        'GuzzleHttp\\Pool' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Pool.php',
        'GuzzleHttp\\PrepareBodyMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php',
        'GuzzleHttp\\Promise\\AggregateException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/AggregateException.php',
        'GuzzleHttp\\Promise\\CancellationException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/CancellationException.php',
        'GuzzleHttp\\Promise\\Coroutine' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Coroutine.php',
        'GuzzleHttp\\Promise\\Create' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Create.php',
        'GuzzleHttp\\Promise\\Each' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Each.php',
        'GuzzleHttp\\Promise\\EachPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/EachPromise.php',
        'GuzzleHttp\\Promise\\FulfilledPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/FulfilledPromise.php',
        'GuzzleHttp\\Promise\\Is' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Is.php',
        'GuzzleHttp\\Promise\\Promise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Promise.php',
        'GuzzleHttp\\Promise\\PromiseInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/PromiseInterface.php',
        'GuzzleHttp\\Promise\\PromisorInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/PromisorInterface.php',
        'GuzzleHttp\\Promise\\RejectedPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/RejectedPromise.php',
        'GuzzleHttp\\Promise\\RejectionException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/RejectionException.php',
        'GuzzleHttp\\Promise\\TaskQueue' => __DIR__ . '/..' . '/guzzlehttp/promises/src/TaskQueue.php',
        'GuzzleHttp\\Promise\\TaskQueueInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/TaskQueueInterface.php',
        'GuzzleHttp\\Promise\\Utils' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Utils.php',
        'GuzzleHttp\\Psr7\\AppendStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/AppendStream.php',
        'GuzzleHttp\\Psr7\\BufferStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/BufferStream.php',
        'GuzzleHttp\\Psr7\\CachingStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/CachingStream.php',
        'GuzzleHttp\\Psr7\\DroppingStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/DroppingStream.php',
        'GuzzleHttp\\Psr7\\Exception\\MalformedUriException' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Exception/MalformedUriException.php',
        'GuzzleHttp\\Psr7\\FnStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/FnStream.php',
        'GuzzleHttp\\Psr7\\Header' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Header.php',
        'GuzzleHttp\\Psr7\\HttpFactory' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/HttpFactory.php',
        'GuzzleHttp\\Psr7\\InflateStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/InflateStream.php',
        'GuzzleHttp\\Psr7\\LazyOpenStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/LazyOpenStream.php',
        'GuzzleHttp\\Psr7\\LimitStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/LimitStream.php',
        'GuzzleHttp\\Psr7\\Message' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Message.php',
        'GuzzleHttp\\Psr7\\MessageTrait' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MessageTrait.php',
        'GuzzleHttp\\Psr7\\MimeType' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MimeType.php',
        'GuzzleHttp\\Psr7\\MultipartStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MultipartStream.php',
        'GuzzleHttp\\Psr7\\NoSeekStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/NoSeekStream.php',
        'GuzzleHttp\\Psr7\\PumpStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/PumpStream.php',
        'GuzzleHttp\\Psr7\\Query' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Query.php',
        'GuzzleHttp\\Psr7\\Request' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Request.php',
        'GuzzleHttp\\Psr7\\Response' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Response.php',
        'GuzzleHttp\\Psr7\\Rfc7230' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Rfc7230.php',
        'GuzzleHttp\\Psr7\\ServerRequest' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/ServerRequest.php',
        'GuzzleHttp\\Psr7\\Stream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Stream.php',
        'GuzzleHttp\\Psr7\\StreamDecoratorTrait' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/StreamDecoratorTrait.php',
        'GuzzleHttp\\Psr7\\StreamWrapper' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/StreamWrapper.php',
        'GuzzleHttp\\Psr7\\UploadedFile' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UploadedFile.php',
        'GuzzleHttp\\Psr7\\Uri' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Uri.php',
        'GuzzleHttp\\Psr7\\UriComparator' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UriComparator.php',
        'GuzzleHttp\\Psr7\\UriNormalizer' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UriNormalizer.php',
        'GuzzleHttp\\Psr7\\UriResolver' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UriResolver.php',
        'GuzzleHttp\\Psr7\\Utils' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Utils.php',
        'GuzzleHttp\\RedirectMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RedirectMiddleware.php',
        'GuzzleHttp\\RequestOptions' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RequestOptions.php',
        'GuzzleHttp\\RetryMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RetryMiddleware.php',
        'GuzzleHttp\\TransferStats' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/TransferStats.php',
        'GuzzleHttp\\Utils' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Utils.php',
        'JmesPath\\AstRuntime' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/AstRuntime.php',
        'JmesPath\\CompilerRuntime' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/CompilerRuntime.php',
        'JmesPath\\DebugRuntime' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/DebugRuntime.php',
        'JmesPath\\Env' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/Env.php',
        'JmesPath\\FnDispatcher' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/FnDispatcher.php',
        'JmesPath\\Lexer' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/Lexer.php',
        'JmesPath\\Parser' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/Parser.php',
        'JmesPath\\SyntaxErrorException' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/SyntaxErrorException.php',
        'JmesPath\\TreeCompiler' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/TreeCompiler.php',
        'JmesPath\\TreeInterpreter' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/TreeInterpreter.php',
        'JmesPath\\Utils' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/Utils.php',
        'Psr\\Http\\Client\\ClientExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/ClientExceptionInterface.php',
        'Psr\\Http\\Client\\ClientInterface' => __DIR__ . '/..' . '/psr/http-client/src/ClientInterface.php',
        'Psr\\Http\\Client\\NetworkExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/NetworkExceptionInterface.php',
        'Psr\\Http\\Client\\RequestExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/RequestExceptionInterface.php',
        'Psr\\Http\\Message\\MessageInterface' => __DIR__ . '/..' . '/psr/http-message/src/MessageInterface.php',
        'Psr\\Http\\Message\\RequestFactoryInterface' => __DIR__ . '/..' . '/psr/http-factory/src/RequestFactoryInterface.php',
        'Psr\\Http\\Message\\RequestInterface' => __DIR__ . '/..' . '/psr/http-message/src/RequestInterface.php',
        'Psr\\Http\\Message\\ResponseFactoryInterface' => __DIR__ . '/..' . '/psr/http-factory/src/ResponseFactoryInterface.php',
        'Psr\\Http\\Message\\ResponseInterface' => __DIR__ . '/..' . '/psr/http-message/src/ResponseInterface.php',
        'Psr\\Http\\Message\\ServerRequestFactoryInterface' => __DIR__ . '/..' . '/psr/http-factory/src/ServerRequestFactoryInterface.php',
        'Psr\\Http\\Message\\ServerRequestInterface' => __DIR__ . '/..' . '/psr/http-message/src/ServerRequestInterface.php',
        'Psr\\Http\\Message\\StreamFactoryInterface' => __DIR__ . '/..' . '/psr/http-factory/src/StreamFactoryInterface.php',
        'Psr\\Http\\Message\\StreamInterface' => __DIR__ . '/..' . '/psr/http-message/src/StreamInterface.php',
        'Psr\\Http\\Message\\UploadedFileFactoryInterface' => __DIR__ . '/..' . '/psr/http-factory/src/UploadedFileFactoryInterface.php',
        'Psr\\Http\\Message\\UploadedFileInterface' => __DIR__ . '/..' . '/psr/http-message/src/UploadedFileInterface.php',
        'Psr\\Http\\Message\\UriFactoryInterface' => __DIR__ . '/..' . '/psr/http-factory/src/UriFactoryInterface.php',
        'Psr\\Http\\Message\\UriInterface' => __DIR__ . '/..' . '/psr/http-message/src/UriInterface.php',
        'Symfony\\Component\\Yaml\\Dumper' => __DIR__ . '/..' . '/symfony/yaml/Dumper.php',
        'Symfony\\Component\\Yaml\\Escaper' => __DIR__ . '/..' . '/symfony/yaml/Escaper.php',
        'Symfony\\Component\\Yaml\\Exception\\DumpException' => __DIR__ . '/..' . '/symfony/yaml/Exception/DumpException.php',
        'Symfony\\Component\\Yaml\\Exception\\ExceptionInterface' => __DIR__ . '/..' . '/symfony/yaml/Exception/ExceptionInterface.php',
        'Symfony\\Component\\Yaml\\Exception\\ParseException' => __DIR__ . '/..' . '/symfony/yaml/Exception/ParseException.php',
        'Symfony\\Component\\Yaml\\Exception\\RuntimeException' => __DIR__ . '/..' . '/symfony/yaml/Exception/RuntimeException.php',
        'Symfony\\Component\\Yaml\\Inline' => __DIR__ . '/..' . '/symfony/yaml/Inline.php',
        'Symfony\\Component\\Yaml\\Parser' => __DIR__ . '/..' . '/symfony/yaml/Parser.php',
        'Symfony\\Component\\Yaml\\Unescaper' => __DIR__ . '/..' . '/symfony/yaml/Unescaper.php',
        'Symfony\\Component\\Yaml\\Yaml' => __DIR__ . '/..' . '/symfony/yaml/Yaml.php',
        'Symfony\\Polyfill\\Ctype\\Ctype' => __DIR__ . '/..' . '/symfony/polyfill-ctype/Ctype.php',
        'Symfony\\Polyfill\\Mbstring\\Mbstring' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/Mbstring.php',
        'clagiordano\\weblibs\\configmanager\\AbstractConfigManager' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/AbstractConfigManager.php',
        'clagiordano\\weblibs\\configmanager\\ArrayConfigManager' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/ArrayConfigManager.php',
        'clagiordano\\weblibs\\configmanager\\ConfigManager' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/ConfigManager.php',
        'clagiordano\\weblibs\\configmanager\\FileConverter' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/FileConverter.php',
        'clagiordano\\weblibs\\configmanager\\IConfigurable' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/IConfigurable.php',
        'clagiordano\\weblibs\\configmanager\\IConvertable' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/IConvertable.php',
        'clagiordano\\weblibs\\configmanager\\JsonConfigManager' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/JsonConfigManager.php',
        'clagiordano\\weblibs\\configmanager\\YamlConfigManager' => __DIR__ . '/..' . '/clagiordano/weblibs-configmanager/src/YamlConfigManager.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit52263655429b3a0d89ff1360d19e9d20::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit52263655429b3a0d89ff1360d19e9d20::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit52263655429b3a0d89ff1360d19e9d20::$classMap;

        }, null, ClassLoader::class);
    }
}
