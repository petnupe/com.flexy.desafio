# com.flexy.desafio

Este aplicativo foi desenvolvido para o controle de produtos cadastrados com passibilidade de serem categorizados por etiquetas (TAGS) conform especificações do cliente.

- TECNOLOGIAS UTILIZADAS
Ambiente
Docker (utilizando containers Linux) 

Linguagem 
Adianti Framework https://www.adianti.com.br/framework
PHP 7.4

Base da dados
SQlite3

- REQUISITOS
Para utilização do aplicativo é necessária a utilização do ambiente docker através dos seguintes comandos (como administrador).

docker pull pemadata/php7.4-docker:latest

docker run -d -p PORT:80 --name NAME pemadata/php7.4-docker
PORT  = porta de sua preferencia (sugestão 8080)
NAME = Nome do container

Após o donwload e montagem do container basta acessar através do browser.
http://localhost:PORT

Da aplicação:

A aplicação é formada por três itens básicos:
Home:
 - Onde são exibidas as informações das TAGS mais utilizadas.

Product:
- Gerenciamento dos produtos sendo possível incluir, editar, remover e pesquisar produtos, além de atribuir imagens e TAGs.

Tag:
- Gerenciamento das TAGs sendo possível incluir, editar, remover e pesquisar  TAGs.

O código fonte está disponível para distribuição através do link.
https://github.com/petnupe/com.flexy.desafio
