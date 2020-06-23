# CLIENTE ENVIO DE SMS

Serviço de envio de SMS para todas as operadoras e recebimento de status de entrega e respostas dos usuários de forma automática.

## Preparando instalação

No arquivo `composer.json` adicione:

```
{
	...,
	"repositories": [
		{
			"type": "vcs",
			"url": "https://gitlab.com/procedo/client-sms-php.git"
		}
	],
}
```

## Instalando

Para instalar a nova versão da biblioteca é necessário executar o comando abaixo:

```
$ dex composer require procedo/client-sms-php:"dev-master"
```

Será solicitado o acesso do usuário no GitLab, o mesmo deverá ser membro do projeto "client-sms-php", caso contrário a dependência não será instalada.

## Exemplo de uso

Importando e inicializando a classe
```
// Carrega a classe
use Procedo\SendMultiple;

// Inicializa a classe de envio de multiplas mensagens
$smsMultiple = new SendMultiple();

// Variavel de ambiente ( development, testing, production )
$smsMultiple->setEnvironment(env('APP_ENV'));

// Token de autenticação ( cada projeto tem um token )
$smsMultiple->setAccessToken(env('API_SMS_TOKEN'));

```

Para utilizar o envio de SMS é necessário que as mensagens fiquem no formato abaixo
```
// Preparação de dados SMS
$sms = [
	[
		'celular' => $client['telefone_whatsapp'], 
		'mensagem'=> 'Clique no link para que seu cadastro seja efetivado',
		'link' => ProcedoHelper::getUrlHost() . "ativarCliente/" .  md5( $client['codigo'] )
	],
	[
		'celular' => $client['telefone_whatsapp'], 
		'mensagem'=> 'Clique no link para que seu cadastro seja efetivado',
		'link' => ProcedoHelper::getUrlHost() . "ativarCliente/" .  md5( $client['codigo'] )
	]
];
```

```
// Chamada para envio de SMS
$envia = $smsMultiple->sendSms($sms);

if( $envia->status == TRUE ){
	// Sucesso
} else {
	// Error
	throw new Exception($envia->mensagem);
}
```