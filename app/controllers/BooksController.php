<?php

class BooksController extends ControllerBase
{
    public function searchAction()
    {
		$books = new Books();
		$books->searchBooksFromAmazon(null);
    }

}

