<?php

namespace Screeper\JsonApiBundle\Services;

/**
 * @author Graille
 * @version 1.0.0
 * @link http://github.com/Graille
 * @package JSONAPIBUNDLE
 * @since 1.0.0
 */

use Screeper\JsonApiBundle\Model\JsonApi as JsonApiEntity;
use Screeper\ServerBundle\Entity\Server as ServerEntity;
use Screeper\ServerBundle\Services\ServerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JsonApiService
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Récupération du serveur
     * @param $server_name
     * @return ServerEntity
     */
    protected function getServer($server_name)
    {
        return $this->container
            ->get('screeper.server.services.server')
            ->getServer($server_name);
    }

    /**
     * @param string $server_name
     * @return JsonApiEntity
     */
    public function getApi($server_name = ServerService::DEFAULT_SERVER_NAME)
    {
        $server = $this->getServer($server_name);

        $API = new JsonApiEntity(
            $server->getConfigIp(),
            $server->getConfigPort(),
            $server->getConfigLogin(),
            $server->getConfigPassword(),
            $server->getConfigSalt());

        return $API;
    }

    /**
     * Fonction calls : Permet d'envoyer une commande au serveur
     * @param $command
     * @param array $options
     * @param $server
     * @return array
     */
    public function call($command, array $options = array(), $server = ServerService::DEFAULT_SERVER_NAME)
    {
        $result = $this->getApi($server)->call($command, $options);

        return $result;
    }

    /**
     * @param $command
     * @param array $options
     * @param $server
     * @return array
     */
    public function callResult($command, array $options = array(), $server = ServerService::DEFAULT_SERVER_NAME) // Un call suivie d'un verif
    {
        $result = $this->call($command, $options, $server);

        return $this->checkResult($result);
    }

    /**
     * Fonctions de vérification du résultat d'une requete "call"
     * @param $result
     * @return array
     */
    public function checkResult($result) // Renvoit le paramètre "sucess" d'une requete si celui-ci existe
    {
        return ($result[0]['result'] == 'success') ? $result[0]['success'] : array();
    }

    // Quelques fonctions utiles

    /**
     * @param $server
     * @return array
     */
    public function getPlayersOnline($server = ServerService::DEFAULT_SERVER_NAME)
    {
        return $this->callResult('players.online', array(), $server);
    }

    /**
     * @param $player
     * @param $server_name
     * @return array
     */
    public function getGroups($player, $server_name = ServerService::DEFAULT_SERVER_NAME)
    {
        return $this->callResult('permissions.getGroups', array($player), $server_name);
    }

    /**
     * @param $user
     * @param $grade
     * @param $server_name
     */
    public function gradeUser($user, $grade, $server_name = ServerService::DEFAULT_SERVER_NAME)
    {
        $this->call('runConsoleCommand', array('pex user '.$user.' group set '.$grade), $server_name);
    }

    /**
     * @param $message
     * @param string $name
     * @param string $server_name
     */
    public function writeMessage($message, $name = 'Server', $server_name = ServerService::DEFAULT_SERVER_NAME)
    {
        //$this->call('runConsoleCommand', array('say '.$message), $server_name);
        $this->call('chat.with_name', array($message, $name));
    }
    /**
     * @param $server_name
     * @return bool
     */
    public function getServerStatus($server_name = ServerService::DEFAULT_SERVER_NAME)
    {
        $maxPlayers = $this->callResult("getPlayerLimit", array(), $server_name); // La variable maxJoueurs correspond au nombre de slots

        return ($maxPlayers == 0 ) ? false : true;
    }
}