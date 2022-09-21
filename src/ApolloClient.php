<?php
/**
 * 阿波罗配置中心
 * @author      church<wolfqong1993@gmail.com>
 * @since        1.0
 */

namespace Church\WebmanApolloPlugin;

use Church\ApolloClient\Client;

class ApolloClient
{
    public function onWorkerStart()
    {
        $namespaces = getenv('APOLLO_NAMESPACES');
        $host = getenv('APOLLO_HOST');
        $appid = getenv('APOLLO_APPID');
        $secret = getenv('APOLLO_SECRET');

        if (!$host || !$appid || !$secret) {
            throw new \Exception('请先在.env文件中设置APOLLO_HOST、APOLLO_APPID、APOLLO_SECRET、APOLLO_NAMESPACES');
        }

        $namespaces = explode(',', $namespaces);
        $client = new Client(getenv('APOLLO_HOST'), getenv('APOLLO_APPID'), $namespaces[0], getenv('APOLLO_SECRET'));

        $client->autoPull($namespaces, function($configuration) {
            $configuration = json_decode($configuration, true);
            $namespaceNameParts = explode('.', $configuration['namespaceName']);

            foreach ($namespaceNameParts as $key=>$namespaceNamePart) {
                $namespaceNameParts[$key] = ucfirst($namespaceNamePart);
            }

            $namespaceName = lcfirst(join('', $namespaceNameParts));

            $path = [config_path(), 'plugin', 'church', 'webman-apollo-plugin'];
            file_put_contents(join(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . $namespaceName . '.php', '<?php return ' . var_export($configuration, true) . ';');
        });
    }
}