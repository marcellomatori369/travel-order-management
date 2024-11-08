# Teste-Onfly-PHP

Microsserviço para gerenciar pedidos de viagem corporativa o teste da vaga de desenvolvedor php.

## Informações preliminares

1) O projeto foi configurado utilizando o laravel sail `https://laravel.com/docs/11.x/sail`, então no momento de subir o container docker pela primeira vez pode ocorrer uma pequena demora.
2) Existem dois tipos de usuários que podem ser cadastrados no serviço:
- Interno: contêm controle total sobre o serviço, podendo atualizar status de pedidos e visualizar pedidos de todos os outros usuários. Para criar um usuário interno basta utilizar o dominio onfly.com no cadastro do email (ex: marcello@onfly.com). 
- Padrão: contêm ações limitadas sobre o serviço podendo somente se cadastrar, logar, fazer um pedido de viagem e visuaizar seus pedidos.
3) Os pedidos de viagens são somente para cidades do Brasil, isso foi uma opção minha para tornar um pouco mais dinâmico a utilização do serviço. Foi utilizado a api `https://brasilapi.com.br/docs` para efetuar as validações dos destinos inseridos.

## Requisitos

- [Git]
- [Docker]
- [Docker-compose]
- [Ferramenta-de-processamento-API-REST] (utilizei o insomia com estes arquivos de rota: )

## Build das imagens Docker

Execute o seguinte comando na raiz do projeto para configurar as imagens:

`./vendor/bin/sail up`

## Configuração

Como o projeto foi configurado com laravel sail, não vamos usar o comando padrão do docker `docker compose exec ...`, em vez disso utilizaremos `./vendor/bin/sail ...`.

### Configuração do ambiente

1) Copie o arquivo `.env.example` localizado na raiz do projeto para `.env`
2) Execute o comando `./vendor/bin/sail composer install` para instalar as dependências do projeto
3) Gere a chave de criptografia do Laravel com o comando `./vendor/bin/sail php artisan key:generate`
4) Gere o secret do JWT com o comando `./vendor/bin/sail php artisan jwt:secret` (caso ele não seja adicionado direto no seu .env, copie a chave gerada e cole manualmente no `JWT_SECRET=`)
5) Execute as migrations do projeto com `./vendor/bin/sail php artisan migrate` (talvez elas já tenham sido migradas no processo de configurar as imagens Docker)

A url para acessar as rotas criadas é: `http://localhost:8000/api/rota-desejada`

### Executando testes

- Executar todos os testes: `./vendor/bin/sail test`
- Executar apenas um arquivo teste: `./vendor/bin/sail test tests/Caminho/Do/Teste/OnflyTest.php`
- Executar apenas um teste dentro do arquivo teste: `./vendor/bin/sail test tests/Caminho/Do/Teste/OnflyTest.php --filter=test_if_did_you_understand`
