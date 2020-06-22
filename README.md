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