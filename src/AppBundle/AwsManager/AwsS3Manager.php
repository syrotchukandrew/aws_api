<?php
namespace AppBundle\AwsManager;

use Aws\S3\S3Client;

class AwsS3Manager
{
    private $region;
    private $version;
    private $key;
    private $secret;
    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;


    public function __construct($region, $version, $key, $secret, $bucket)
    {
        $this->region = (string) $region;
        $this->version = $version;
        $this->key = $key;
        $this->secret = $secret;
        $this->bucket = $bucket;
        $this->client = $this->createClient();
    }

    public function createClient()
    {
        return $client = new S3Client([
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret
            ]]);

    }

    /**
     * @return \Iterator
     */
    public function getAllObject()
    {
        $objects = $this->client->getIterator('ListObjects', [
            'Bucket' => $this->bucket
        ]);

        $objects_array = [];
        foreach ($objects as $object) {
            $objects_array[] = $object;
        }

        return $objects_array;
    }

    /**
     * @param $key
     * @param $SourceUrl
     * @return \Aws\Result
     */
    public function putObject($key, $SourceUrl)
    {
        $result = $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SourceFile' => $SourceUrl
        ]);

        return $result;
    }

    /**
     * @param $key
     */
    public function removeObject($key)
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);

    }
}