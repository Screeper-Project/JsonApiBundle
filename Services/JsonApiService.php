<?php

namespace GrailleLabs\JsonApiBundle\Services;

/**
 * @author Graille
 * @version 1.0.0
 * @link http://github.com/Graille
 * @package JSONAPIBUNDLE
 * @since 1.0.0
 */


use GrailleLabs\JsonApiBundle\Entity\JsonApi as JsonApiEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JsonApiService
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Récupération des serveurs
    protected function getServers()
    {
        return $this->container->getParameter('gob.json_api.servers');
    }
    protected function checkConfig($server, $servers_list)
    {
        if (!isset($servers_list[$server]))
            throw new \InvalidArgumentException('JsonAPIBundle - le serveur "'.$server.'" ne possède aucune configuration dans app/config/config.yml');
    }
    protected function getConfig($server_name) // Récupération de la configuration d'un serveur
    {
        $servers_list = $this->getServers();

        $this->checkConfig($server_name, $servers_list);

        $server_config = $servers_list[$server_name];

        if (!isset($server_config['login']) or
          !isset($server_config['password']) or
          !isset($server_config['ip']))
            if (isset($server_config['pattern'])) { // Si c'est un pattern
                $this->checkConfig($server_config['pattern'], $servers_list);
                $config = $this->getConfig($server_config['pattern']);

                foreach($server_config as $key => $sub_config) // On écrase les configurations copié par celles de l'utilisateur
                    $config[$key] = $server_config;

                $server_config = $config; // Enfin on renvoi la nouvelle config
            }
            else
                throw new \InvalidArgumentException('JsonAPIBundle - le serveur "'.$server_name.'" est mal configuré');
        else {
            if(!isset($server_config['port']))
                $server_config['port'] = 20059;
            if(!isset($server_config['salt']))
                $server_config['salt'] = "";
        }

        return $server_config;
    }

    public function getApi($server = 'default')
    {
        $config = $this->getConfig($server);
        $API = new JsonApiEntity($config['ip'], $config['port'], $config['login'], $config['password'], $config['salt']);

        return $API;
    }

    // Fonction calls : Permet d'envoyer une commande au serveur
    public function call($command, array $options = array(), $server = 'default')
    {
        $result = $this->getApi($server)->call($command, $options);
        return $result;
    }
    public function callResult($command, array $options = array(), $server = 'default') // Un call suivie d'un verif
    {
        $result = $this->call($command, $options, $server);
        return $this->checkResult($result);
    }

    // Fonctions de vérifiations diverses
    public function checkResult($result)
    {
        if ($result[0]['result'] == 'success')
            return $result[0]['success'];
        else
            return array();
    }

    // Quelques fonctions utiles
    public function getPlayersOnline($server = 'default')
    {
        return $this->callResult('players.online', array(), $server);
    }
    public function getGroups($player, $server = 'default')
    {
        return $this->callResult('permissions.getGroups', array($player), $server);
    }
    public function gradeUser($user, $grade, $server = 'default')
    {
        $this->call('runConsoleCommand', array('pex user '.$user.' group set '.$grade), $server);
    }
    public function getServerStatus($server = 'default')
    {
        $maxPlayers = $this->callResult("getPlayerLimit", array(), $server = 'default'); // La variable maxJoueurs correspond au nombre de slots

        return ($maxPlayers == 0 ) ? false : true;
    }
}