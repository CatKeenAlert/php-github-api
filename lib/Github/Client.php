<?php

namespace Github;

use Github\Api;
use Github\HttpClient\HttpClientInterface;
use Github\HttpClient\HttpClient;

/**
 * Simple yet very cool PHP Github client
 *
 * @tutorial  http://github.com/ornicar/php-github-api/blob/master/README.markdown
 * @version   3.2
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 *
 * Website: http://github.com/ornicar/php-github-api
 * Tickets: http://github.com/ornicar/php-github-api/issues
 */
class Client
{
    /**
     * Constant for authentication method. Indicates the default, but deprecated
     * login with username and token in URL.
     */
    const AUTH_URL_TOKEN = 'url_token';

    /**
     * Constant for authentication method. Indicates the new favored login method
     * with username and password via HTTP Authentication.
     */
    const AUTH_HTTP_PASSWORD = 'http_password';

    /**
     * Constant for authentication method. Indicates the new login method with
     * with username and token via HTTP Authentication.
     */
    const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * The httpClient instance used to communicate with GitHub
     *
     * @var HttpClientInterface
     */
    protected $httpClient = null;

    /**
     * The list of loaded API instances
     *
     * @var array
     */
    protected $apis = array();

    /**
     * Instanciate a new GitHub client
     *
     * @param HttpClientInterface $httpClient custom http client
     */
    public function __construct(HttpClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new HttpClient();
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string      $login  GitHub username
     * @param string      $secret GitHub private token or Github password if $method == AUTH_HTTP_PASSWORD
     * @param null|string $method One of the AUTH_* class constants
     */
    public function authenticate($login, $secret, $method = null)
    {
        $this->httpClient->setOption('auth_method', $method ?: self::AUTH_URL_TOKEN);

        if (self::AUTH_HTTP_PASSWORD) {
            $this->httpClient->setOption('login', $login)
                             ->setOption('password', $secret);
        } else {
            $this->httpClient->setOption('token', $secret);
        }
    }

    /**
     * Deauthenticate a user for all next requests
     */
    public function deAuthenticate()
    {
        $this->authenticate(null, null, null);
    }

    /**
     * Call any path, GET method
     * Ex: $api->get('repos/show/my-username/my-repo')
     *
     * @param   string  $path            the GitHub path
     * @param   array   $parameters       GET parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function get($path, array $parameters = array(), $requestOptions = array())
    {
        return $this->httpClient->get($path, $parameters, $requestOptions);
    }

    /**
     * Call any path, POST method
     * Ex: $api->post('repos/show/my-username', array('email' => 'my-new-email@provider.org'))
     *
     * @param   string  $path            the GitHub path
     * @param   array   $parameters       POST parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function post($path, array $parameters = array(), $requestOptions = array())
    {
        return $this->httpClient->post($path, $parameters, $requestOptions);
    }

    /**
     * Get the http client.
     *
     * @return HttpClientInterface a request instance
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Inject another http client
     *
     * @param HttpClientInterface $httpClient The httpClient instance
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get the user API
     *
     * @return  Api\User    the user API
     */
    public function getUserApi()
    {
        if (!isset($this->apis['user'])) {
            $this->apis['user'] = new Api\User($this);
        }

        return $this->apis['user'];
    }

    /**
     * Get the issue API
     *
     * @return  Api\Issue   the issue API
     */
    public function getIssueApi()
    {
        if (!isset($this->apis['issue'])) {
            $this->apis['issue'] = new Api\Issue($this);
        }

        return $this->apis['issue'];
    }

    /**
     * Get the commit API
     *
     * @return  Api\Commit  the commit API
     */
    public function getCommitApi()
    {
        if (!isset($this->apis['commit'])) {
            $this->apis['commit'] = new Api\Commit($this);
        }

        return $this->apis['commit'];
    }

    /**
     * Get the repo API
     *
     * @return  Api\Repo  the repo API
     */
    public function getRepoApi()
    {
        if (!isset($this->apis['repo'])) {
            $this->apis['repo'] = new Api\Repo($this);
        }

        return $this->apis['repo'];
    }

    /**
     * Get the organization API
     *
     * @return  Api\Organization  the object API
     */
    public function getOrganizationApi()
    {
        if (!isset($this->apis['organization'])) {
            $this->apis['organization'] = new Api\Organization($this);
        }

        return $this->apis['organization'];
    }

    /**
     * Get the object API
     *
     * @return  Api\Object  the object API
     */
    public function getObjectApi()
    {
        if (!isset($this->apis['object'])) {
            $this->apis['object'] = new Api\Object($this);
        }

        return $this->apis['object'];
    }

    /**
     * Get the pull request API
     *
     * @return  Api\PullRequest  the pull request API
     */
    public function getPullRequestApi()
    {
        if (!isset($this->apis['pullrequest'])) {
            $this->apis['pullrequest'] = new Api\PullRequest($this);
        }

        return $this->apis['pullrequest'];
    }

    /**
     * Inject an API instance
     *
     * @param  string        $name the API name
     * @param  ApiInterface  $api  the API instance
     *
     * @return self
     */
    public function setApi($name, ApiInterface $instance)
    {
        $this->apis[$name] = $instance;

        return $this;
    }

    /**
     * Get any API
     *
     * @param  string       $name the API name
     * @return ApiInterface       the API instance
     */
    public function getApi($name)
    {
        return $this->apis[$name];
    }
}
