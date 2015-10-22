# MelyssaFramework
Framework para aplicações web PHP 5.4+

##Instalação:

Após realizar o download do framework, descompacte-o em uma pasta no seu servidor, existem algumas configurações a serem realizadas antes de começar a usar o sistema.

##Configuração do framework:


###Arquivo Constants.php:

A primeira coisa que devemos fazer é definir algumas constantes para o funcionamento do sistema, dentro do arquivo **Core/Configs/Constants.php**, são elas:

 - **CONTROLLERS**: Caminho para a pasta onde se encontram os controllers da aplicação.
 - **MODELS**: Caminho para a pasta onde se encontram os models da aplicação.
 - **VIEWS**: Caminho para a pasta onde se encontram as views da aplicação.
 - **CONFIGS**: Caminho para a pasta onde se encontram as configurações da aplicação.
 - **LANGUAGE**:  Caminho para a pasta onde se encontram os arquivos de tradução da aplicação.
 - **DEFAULT_LANG**: Linguagem padrão a ser utilizada pelo sistema.
 - **BASE_URL**: Url base da aplicação E.g: http://example.com
 - **LOG_PATH**: Caminho para a pasta de logs do sistema.
 - **SESSION_HASH**: Hash único para segurança de sessões (sha1, md5...), utilizado para proteção contra session-hijacking.
 - **URI_IDENTIFIER**: Identificador de url, utilizado pelo framework para resgatar os dados enviados na url, não há necessidade de alterar este valor.

###Arquivo index.php:

O arquivo index.php atua como front controller do ***Melyssa Framework***, é este arquivo o responsável por tratar as requisições e enviar para os controllers e actions corretos. Para isso precisamos definir algumas configurações, são elas:

 - **VENDOR_PATH**: Caminho da pasta onde se encontra o núcleo do Framework, esta pasta também pode conter arquivos de bibliotecas de terceiros.
 - **APP_PATH**: Caminho da pasta onde se encontram os arquivos da aplicação, controllers, views, formulários, etc.
 - **ENVIRONMENT**: Ambiente da aplicação, utilizado para definir a exibição de erros e de código php, valores possíveis: **Development**, **Testing** e **Production**

##Ambientes:

É possível utilizar configurações com base no ambiente da aplicação, para isso, após definir o ambiente na configuração do sistema, crie uma pasta dentro da pasta de configurações com o nome do ambiente e.g **Development** e salve as configurações dentro desta pasta.

##Configuração de rotas:

A configuração de rotas é feita dentro do arquivo Routes.php, que deve ser armazenado na pasta de configurações da aplicação.

O arquivo Routes.php deve retornar um array contendo as rotas no formato mostrado abaixo:

    <?php
        return array(
            'default-controller' => 'Welcome',
            'default-action' => 'index',
            
            'Welcome' => array(
                'callables' => array(
                    'index' => array(
                        'methods' => array('GET'),
                    ),
                ),
            ),
        );

Como visto, podemos definir um controller padrão a ser carregado quando não houver nada na url, para isso, basta incluir uma chave **default-controller** com o valor sendo o controller correto, também é possível definir uma action padrão, incluindo a chave **default-action**.

Para cada controller precisamos informar os métodos acessíveis via url (callables), se um método não estiver dentro desse array não será possível acessá-lo pela url.

Para cada action (callable), precisamos definir os métodos de acesso aceitos (methods), e também podemos informar parâmetros, passando o nome do parâmetro e uma expressão regular indicando o formato e o tipo de caracteres aceitos. Os parâmetros são filtrados antes de serem enviados à aplicação, parâmetros não informados nas configurações ou que não estejam dentro do padrão solicitado serão ignorados e não estarão disponíveis.

Veremos mais configurações adiante.

##Criando controllers:

Com nossas rotas configuradas, o próximo passo é criar nosso primeiro controller, o Melyssa Framework utiliza namespaces para carregar as classes de forma automática, todos os controllers devem estar dentro do namespace Controllers. É necessário também estender **(extends)** o controller base do framework: ***Melyssa\Mvc\Controller*** como é mostrado abaixo:

    <?php
        namespace Controllers;
        
        use Melyssa\Mvc\Controller;
        
        class Welcome extends Controller
        {
            public function indexAction()
            {
                echo "Hello World !";
            }
        }
Nesse exemplo podemos ver que já definimos uma action index, o Melyssa segue os padrões definidos pelas PSR´s, sendo assim, nomes de métodos e variáveis devem estar em LowerCamelCase e nomes de classes devem estar em StudlyCaps. Veja mais informações sobre as PSR´s em http://www.php-fig.org/psr/psr-1/

Neste exemplo estamos somente *echoando* uma mensagem na tela, podemos carregar uma view, um arquivo html para visualização no navegador, como demonstrado abaixo:


        public function indexAction()
        {
            $this->view("Index");
        }
O Melyssa Framework utiliza convenções para carregar as views, neste caso, a view chamada (Index) será procurada dentro da pasta de views, dentro de uma pasta com o nome do controller (Welcome). Se o arquivo solicitado não for encontrado, será disparada uma exceção.

Vamos criar nossa primeira view. Crie o arquivo **Index.php** dentro da pasta **Application/Views/Welcome**. Para exemplo vamos inserir apenas uma saudação utilizando tags html:

    <h4>Hello World !</h4>
Neste momento, se acessarmos a url da nossa aplicação poderemos ver a mensagem exibida na tela.
