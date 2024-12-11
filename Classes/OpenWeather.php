<?php
require 'Classes/Models/clima.php';
use GuzzleHttp\Client;
class OpenWeather
{

    //public $latitude = '-27.1091287397728';
    //public $longitude = '-48.91347116618133';
    public $cidade = 'Itajai';
    public $appid = '0abbf85f9de71c15457c4496cb54c2ca';

    public function getTempoAtual()//Chama a Api
    {
        try {
        $recurso = "https://api.openweathermap.org/data/2.5/weather?q=".$this->cidade."&appid=".$this->appid."&lang=pt_br";

        $client = new GuzzleHttp\Client();
        $resposta = $client->request('GET', $recurso, []);

        $objJson = json_decode($resposta->getBody());
        $clima = $this->mapear($objJson); //chamando o metodo mapear

        $timezone_offset = $objJson->timezone; //Converter o fuso horário

        $nascerDoSol = new DateTime();
        $nascerDoSol->setTimestamp($clima->nascerDoSol + $timezone_offset);
        $clima->nascerDoSol = $nascerDoSol->format('H:i');

        $porDoSol = new DateTime();
        $porDoSol->setTimestamp($clima->porDoSol + $timezone_offset);
        $clima->porDoSol = $porDoSol->format('H:i');

        $this->guardarEmCache($clima); //Guardando os dados em cache

        

        }catch (\Exception $e){
            $clima = $this->obterDoCache();

        }
        return $clima;
    }

    private function mapear($objJson){ //chama a classe clima do models
        $clima = new Clima();
        $clima->temperatura = $objJson->main->temp - 273.15;
        $clima->cidade = $objJson->name;
        $clima->umidade = $objJson->main->humidity;
        $clima->direcaoDoVento = $objJson->wind->deg;
        $clima->velocidadeDoVento = $objJson->wind->speed;
        $velocidadeVentoMetrosPorSegundo = $objJson->wind->speed;
        $clima->velocidadeDoVento = number_format($velocidadeVentoMetrosPorSegundo, 2);
        $clima->sensacaoTermica= $objJson->main->feels_like - 273.15;
        $clima->descricao = $objJson->weather[0]->description;
        $clima->temperaturaMaxima = $objJson->main->temp_max - 273.15;
        $clima ->temperaturaMinima = $objJson->main->temp_min - 273.15;
        $clima->icone = $objJson->weather[0]->icon;
        $clima->nascerDoSol = $objJson->sys->sunrise;
        $clima->porDoSol= $objJson->sys->sunset;
        return $clima;
    }

    public function guardarEmCache($clima){
        $dadosSerializados = serialize($clima);//pega os dados da memoria ram e joga pra uma variavel

        $caminhoArquivoCache = 'Cache/clima.bin';
        
        file_put_contents($caminhoArquivoCache, $dadosSerializados); //Guarda os arquivos na variavel 

    }

    public function obterDoCache(){
        $caminhoArquivoCache = 'Cache/clima.bin';

        $dadosSerializados = file_get_contents($caminhoArquivoCache);

        $dadosDeserializados = unserialize($dadosSerializados);

        return $dadosDeserializados;
    }
}