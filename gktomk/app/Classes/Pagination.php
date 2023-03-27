<?php


namespace GKTOMK\Classes;


class Pagination
{
    public int $total = 0;
    public int $page = 1;
    public int $limit = 20;
    public int $num_links = 5;
    public string $url = '';
    public string $text_first = '|&lt;';
    public string $text_last = '&gt;|';
    public string $text_next = '&gt;';
    public string $text_prev = '&lt;';

    public function __construct()
    {
        $this->text_prev = '<';

        $this->text_next = '>';
    }

    public function render() {
        $total = $this->total;

        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (!(int)$this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);
        //$this->url = $_SERVER['QUERY_STRING'];
        $this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

        $output = '<nav aria-label="Page navigation example"><ul class="pagination">';

        if ($page > 1) {
            $output .= '<li class="page-item"><a class="page-link" href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_first . '</a></li>';

            if ($page - 1 === 1) {
                $output .= '<li class="page-item"><a class="page-link" href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_prev . '</a></li>';
            } else {
                $output .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a></li>';
            }
        }

        if ($num_pages > 1) {
            if ($num_pages <= $num_links) {
                $start = 1;
                $end = $num_pages;
            } else {
                $start = $page - floor($num_links / 2);
                $end = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end += abs($start) + 1;
                    $start = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end = $num_pages;
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                } else {
                    if ($i === 1) {
                        $output .= '<li class="page-item"><a class="page-link" href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $i . '</a></li>';
                    } else {
                        $output .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
                    }
                }
            }
        }

        if ($page < $num_pages) {
            $output .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a></li>';
            $output .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a></li>';
        }

        $output .= '</ul></nav>';

        if ($num_pages > 1) {
            return $output;
        } else {
            return '';
        }
    }
}