# pandemia#
Junior Developer Coding Test

Certifique-se de ter lido todo o documento antes de iniciar e siga todas as orientações

## Descrição do Problema

O mundo não estava preparado para a Pandemia do PARALISA-VIRUS! O vírus se espalhou rapidamente atingindo populações inteiras de todas as cidades do mundo. Todos os laboratórios de biotecnologia iniciaram uma corrida frenética contra o relógio a fim de produzir vacinas e remédios contra a doença. Como consequência do rápido espalhamento, todas as pessoas foram obrigadas a ficar enclausuradas sem poder respirar o ar das ruas, sob pena de se contaminar e ficar imóvel permanentemente (algumas pessoas conseguem restabelecer seus movimentos, mas não se sabe o por quê).

Sabendo que, no momento que as vacinas forem descobertas e os remédios desenvolvidos, haverá uma corrida desenfreada para aquisição da cura e da imunização, você, um desenvolvedor em PHP em início de carreira, pretende deixar seu legado para a humanidade implantando um serviço de agendamento de vacinas e remédios via internet. Afinal, a Web continua sendo o principal canal de negócios nesse mundo apocalíptico.


## Requisitos

Você deverá desenvolver uma aplicação ***WEB usando Apache/MySQL/PHP e o framework Codeigniter 4*** que deverá fazer o agendamento de entrega das vacinas e dos remédios, que será feita por um drone (esses não paralizam com o vírus).

Para isso, sua aplicação deverá possuir os seguintes requisitos funcionais:

- **Cadastrar uma solicitação de vacina**

  Caso você ainda não tenha sido contaminado pelo PARALISA-VIRUS, você poderá requisitar a entrega de uma vacina na sua casa. Você deverá digitar seu nome completo e seu CPF. O sistema deverá usar esses dados juntamente com a data e hora da solicitação (busca automática) para calcular a data de entrega.

  Como só é possível fazer 4 entregas por dia, a data da entrega deverá ser calculada por meio da quantidades de entregas feitas antes da sua. Para cada 4 entregas, temos 1 dia de espera. Isso significa que se o pedido for feito no dia 10 de junho e houver 20 pedidos na sua frente, então a data de entrega da sua vacina será 16 de junho ( 20 / 4 = 5 dias. Haverá entrega do dia 10 até o dia 15 de março e a sua será feita no dia 16)

- **Cadastrar uma solicitação de remédio**

  Para o caso de você ter sido contaminado mas, milagrosamente, ter voltado a se mover, então você poderá solicitar um remédio que eliminará o vírus do seu corpo. Para isso, você deverá preencher seu nome completo, seu CPF, a data em que você foi contaminado e a que você voltou a se mover.

  Você pode fazer a solicitação para outra pessoa que não consegue se mover e deixar o campo da data que voltou a se movimentar em branco.

  A regra para calcular a entrega do remédio é a mesma para a entrega da vacina.

- **Cancelar a solicitação do remédio ou da vacina**

  É possível que uma pessoa que tenha solicitado vacina ou remédio venha a morrer de alguma fatalidade (foi pegar o saco de salgadinhos no armário e caiu da cadeira, se engasgando com um pedaço de biscoito!) Dessa forma, você poderá cancelar a solicitação dessa pessoa para que a fila de entregas possa caminhar.

  O usuário deverá digitar seu CPF e, ao exibir as solicitações daquele usuário, você poderá escolher que a solicitação foi cancelada.

- **Marcar a solicitação do remédio / vacina como entregue**

  Como não há fiscais que possam acompanhar as entregas, somos obrigados a confiar uns nos outros. Dessa forma, um usuário poderá sinalizar assim que a sua entrega for recebida. O usuário deverá digitar seu CPF e, ao exibir as solicitações daquele usuário, você poderá escolher que a solicitação foi entregue.

- **Relatório**

  Sua aplicação deverá exibir 3 tipos de relatórios:

    1. Relatório de entregas concluídas
    1. Relatório de entregas canceladas
    3. Relatório de entregas pendentes

---------------------------------------

## Observações

1. Utilize o Apache1, Banco MySQL e PHP7 com framework Codeigniter
2. Não esqueça de enviar o script SQL do seu banco de dados para que possamos recriar o mesmo ambiente.
3. Não é necesário janela de autenticação, afinal, confiamos no bom senso das pessoas durante uma pandemia!
4. Seu código deve ser minimamente legível e compreensível. Se precisar, poderá usar um template **open source para as páginas html/js/css**.
5. Se julgar necessário, documente partes do seu código para facilitar a compreensão
6. Sua solução deverá abordar minimamente os requisitos acima. Entretanto, você poderá adicionar melhorias ou personalizações de acordo com sua experiência
7. Observe o tempo de tarefa e não se perca em detalhes que podem atrasar seu desenvolvimento. Lembre-se que quanto mais tempo você levar, mais pessoas poderão ficar paralisadas (inclusive você)
8. Para realizar commits, escreva mensagens curtas e claras.

## Q&A

> Para onde devo enviar meu código?

Faça um fork desse repositório e envie um **pull request** quando finalizar.

> Se quiser fazer uma pergunta?

Crie uma nova issue no repositório e responderemos o mais brevemente possível

> COmo devo enviar meu projeto?

Envie-nos instruções claras de como rodar seu projeto (configurações de database, dados iniciais das tabelas, etc)

**Original test written by [Ricardo]**

## Author Saulo Albuquerque
Prezados(as).
A aplicação possui:
1 - Variável de ambiente .config.ini que possui todas as informações de acesso ao banco.
2 - Sql na pasta Migrations.
3 - O desenvolvimento do sistema é focado na arquitetura rest, com os protocolos HTTP, feito único e exclusivamente com o PHP, consumindo com JS(jquery)/HTML/CSS/bootstrap, facilitando as integrações com microserviços e se caso necessário for uma possível atualização.
4 - Google charts JS, para criação de gráficos, na pagina de relatórios.
5 - Foi utilizada boas praticas de programação para melhor interpretação, entretanto algumas functions foram documentadas para ajudar na avaliação do código.
6 - Informo-vos, que sou apto para criação da documentação da API com a ferramenta Swagger, se caso necessário for.
7 - Informo-vos ainda, que deixei em aberto a parte do SESSION e AUTH, para uma possível criação de JWT com a criptografia SHA256 e login de usuários para validação de requisições no projeto, deixando assim o mais seguro.
8 - Desde já agradeço a oportunidade e fico feliz por participar deste seletivo, estou de prontidão para quaisquer dúvidas sobre o projeto.