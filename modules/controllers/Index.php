<?php
namespace Controllers;

use Models\News;
use Models\Categories;

class Index extends Controller
{
	private $newsModel;
	private $categoryModel;

	public function __construct()
	{
		parent::__construct();
		$this->newsModel = new News();
		$this->categoryModel = new Categories();
	}

	public function index()
	{
		$latestNews = $this->newsModel->get_news_with_details(
			'',
			null,
			'uploaded_at DESC',
			0,
			\Settings\LATEST_NEWS_COUNT
		);

		$categories = $this->categoryModel->get_all_records();

		$this->render('index', [
			'picts' => $latestNews,
			'cats' => $categories,
			'site_title' => 'Главная'
		]);
	}
}