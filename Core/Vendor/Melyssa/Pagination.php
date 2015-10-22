<?php

namespace Melyssa;

/**
 * Classe de paginação do sistema:
 *
 * Cria links dinâmicos para paginação de resultados.
 *
 * @package		Melyssa Framework
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/
 *
 */

class Pagination
{
    /**
     * Número total de linhas:
     * @var int
     */
    private $totalRows;

    /**
     * Número total de páginas:
     * @var int
     */
    private $totalPages;

    /**
     * Marcador de página, ex http://www.dominio.com/resultados/[$pageName]/1:
     * @var string
     */
    private $pageName = 'pag';

    /**
     * Total de resultados a serem exibidos por página:
     * @var int
     */
    private $perPage;

    /**
     * Página atual do sistema:
     * @var string
     */
    private $actualPage;

    /**
     * Url para onde os links da paginação devem apontar:
     * @var string
     */
    private $linkPages = '/anuncios/buscar';

    /**
     * Tag de abertura do elemento de paginação:
     * @var string
     */
    private $openContainer = '<div id="paginacao-resultados">';

    /**
     * Tag de fechamento do elemento de paginação:
     * @var string
     */
    private $closeContainer = '</div>';

    /**
     * Classe CSS para display da página atual:
     * @var string
     */
    private $actualPageClass = 'l-atual';

    /**
     * Classe CSS para display da página anterior:
     * @var string
     */
    private $prevPageClass = 'l-prev';

    /**
     * Classe CSS para display da próxima página:
     * @var string
     */
    private $nextPageClass = 'l-next';

    /**
     * Construtor da classe:
     *
     * @param int $totalRows Número de registros a serem paginados.
     * @param int $perPage   Número de resultados a serem exibidos por página.
     * @access public
     */
    public function __construct($totalRows, $perPage = 1)
    {
        $this->perPage = $perPage;
        $this->totalRows = $totalRows;
        $this->setPages();
        $this->setCurrentPage();
    }

    public function setLinkPages($pagelink)
    {
        $this->linkPages = $pagelink;
        return $this;
    }

    /**
     * Setando total de páginas:
     *
     * @access private
     */
    private function setPages()
    {
        //Se o total de linhas for maior que o limite por página, setamos o total de páginas:
        if ($this->totalRows > $this->perPage) {
            $this->totalPages = ceil($this->totalRows / $this->perPage);
        } else {
            $this->totalPages = '1';
        }
    }

    /**
     * Setando página atual do sistema:
     *
     * @access private
     */
    private function setCurrentPage()
    {
        $uri = new Uri();
        if ($uri->getParam($this->pageName) == null || $uri->getParam($this->pageName) <= 1) {
            $this->actualPage = '1';
        } elseif ($uri->getParam($this->pageName) >= $this->totalPages) {
            $this->actualPage = $this->totalPages;
        } else {
            $this->actualPage = $uri->getParam($this->pageName);
        }
    }

    /**
     * Criação de link da paginação:
     *
     * @param string $page O número da página a ser linkada
     * @access private
     */
    private function makeLink($page)
    {
        //Verificando se temos a palavra pagina na url atual:
        if (strpos($this->linkPages, '/') == strlen($this->linkPages)) {
            //Temos a barra então só colocamos a palavra pagina:
            return '<a href="' . $this->linkPages . $this->pageName . '/' . $page . '">' . $page . '</a>';
        } else {
            return '<a href="' . $this->linkPages . '/' . $this->pageName . '/' . $page . '">' . $page . '</a>';
        }
    }

    private function makePrevLink()
    {
        $actual = $this->actualPage;
        $prev = $actual - 1;

        if ($prev != 0) {
            //Verificando se temos a palavra pagina na url atual:
            if (strpos($this->linkPages, '/') == strlen($this->linkPages)) {
                //Temos a barra então só colocamos a palavra pagina:
                return '<a href="' . $this->linkPages . $this->pageName . '/' . $prev . '" class="'. $this->prevPageClass .'">&laquo;</a>';
            } else {
                return '<a href="' . $this->linkPages . '/' . $this->pageName . '/' . $prev . '" class="'. $this->prevPageClass .'">&laquo;</a>';
            }
        } else {
            return '';
        }
    }

    private function makeNextLink()
    {
        $actual = $this->actualPage;
        $next = $actual + 1;

        if ($next <= $this->totalPages) {
            //Verificando se temos a palavra pagina na url atual:
            if (strpos($this->linkPages, '/') == strlen($this->linkPages)) {
                //Temos a barra então só colocamos a palavra pagina:
                return '<a href="' . $this->linkPages . $this->pageName . '/' . $next . '" class="'. $this->nextPageClass .'">&raquo;</a>';
            } else {
                return '<a href="' . $this->linkPages . '/' . $this->pageName . '/' . $next . '" class="'. $this->nextPageClass .'">&raquo;</a>';
            }
        } else {
            return '';
        }
    }

    private function makeFirstLink()
    {
        if ($this->actualPage > 1 + 4) {
            //Verificando se temos a palavra pagina na url atual:
            if (strpos($this->linkPages, '/') == strlen($this->linkPages)) {
                //Temos a barra então só colocamos a palavra pagina:
                return '<a href="' . $this->linkPages . $this->pageName . '/1" id="first_page">Primeira</a>';
            } else {
                return '<a href="' . $this->linkPages . '/' . $this->pageName . '/1" id="first_page">Primeira</a>';
            }
        } else {
            return '';
        }
    }

    private function makeLastLink()
    {
        if ($this->actualPage < $this->totalPages - 4) {
            $lastPage = $this->totalPages;
            //Verificando se temos a palavra pagina na url atual:
            if (strpos($this->linkPages, '/') == strlen($this->linkPages)) {
                //Temos a barra então só colocamos a palavra pagina:
                return '<a href="' . $this->linkPages . $this->pageName . '/' . $lastPage . '" id="last_page">Última</a>';
            } else {
                return '<a href="' . $this->linkPages . '/' . $this->pageName . '/' . $lastPage . '" id="last_page">Última</a>';
            }
        } else {
            return '';
        }
    }

    /**
     * Criação do link para a página atual:
     *
     * @access private
     */
    private function makeActualPage()
    {
        return '<a class="' . $this->actualPageClass . '">' . $this->actualPage . '</a>';
    }

    /**
     * Criação do elemento de paginação completo:
     *
     * @access private
     */
    private function createLinks()
    {
        $pages = '';
        //Pegando o total de páginas:
        if ($this->totalPages > 1) {
            $pages .= $this->openContainer;

            // Criando link para a primeira página:

            $pages .= $this->makeFirstLink();

            // Criando link para a página anterior:

            $pages .= $this->makePrevLink();

            // Criando paginação estilo google:

            // Setando o máximo de páginas conforme o tipo de dispositivo:
            $user =& UserAgent::getInstance();
            if ($user->isMobile()) {
                $totalToShow = 6;
            } else {
                $totalToShow = 8;
            }

            for ($i = $this->actualPage - 4, $limLinks = $i + $totalToShow;$i <= $limLinks;$i++) {
                if ($i < 1) {
                    $i = 1;
                    $limLinks = 9;
                }
                if ($limLinks > $this->totalPages) {
                    $limLinks = $this->totalPages;
                    $i = $limLinks - $totalToShow;
                }
                if ($i < 1) {
                    $i = 1;
                    $limLinks = $this->totalPages;
                }

                if ($i == $this->actualPage) {
                    $pages .= $this->makeActualPage();
                } else {
                    $pages .= $this->makeLink($i);
                }
            }

            // Criando link para a próxima página:

            $pages .= $this->makeNextLink();

            // Criando link para a última página:

            $pages .= $this->makeLastLink();

            $pages .= $this->closeContainer;
        }

        return $pages;
    }

    /**
     * Método para recuperar a posição atual do sistema:
     *
     * @access public
     */
    public function getActualPage()
    {
        return $this->actualPage;
    }

    public function getPageMarker()
    {
        return $this->actualPage * $this->perPage - $this->perPage;
    }

    public function getPageBreadcrumb()
    {
        return sprintf("P&aacute;gina <strong>%s</strong> de <strong>%s</strong>", $this->actualPage, ($this->totalPages > 1) ? $this->totalPages : 1);
    }

    /**
     * Método para recuperar o elemento resultante da paginação:
     *
     * @access public
     */
    public function getPages()
    {
        return $this->createLinks();
    }

    /**
     * Método para recuperar o total de linhas paginadas:
     *
     * @access public
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }
}
