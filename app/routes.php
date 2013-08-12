<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});


// Route::get('stops_map', function()
// {
// 	$stops = Stop::all();

// 	$dim = new MapDimensions(600, 800);
// 	$bounds = $dim->bounds();

// 	$size = $dim->size();

// 	return View::make('stops-svg')->with('stops', $stops)->with('dim', $dim);
// });

Route::post('stops/fix', array('before' => 'csrf',  function() {

	// print_r(Input::all());

	// die();

	if ($stop_id = Input::get('stop_id')) {
		if ($stop = Stop::find($stop_id)) {
			
			$schema = CSchema::find(1);

			if ($coords = Coordinates::where('stop_id', $stop_id)->where('schema_id', $schema->id)->delete()) { 
				// $coords->delete();
			}

			$newCoords = new Coordinates;
			$newCoords->x = Input::get('pos_x');
			$newCoords->y = Input::get('pos_y');
			$newCoords->cschema()->associate($schema);
			$newCoords->stop()->associate($stop);
			$newCoords->save();

			$newCoords = Coordinates::find($newCoords->id);


			if (Request::ajax())
			{
				return Response::json(
					array(
						'stop_id' => $stop_id,
						'pos_x' => $newCoords->x,
						'pos_y' => $newCoords->y,
						)
					);
			}
			else {
				Redirect::to('stops/fix');
			}
		}
	}
	else {
		// App::abort(404, 'Not Found');
	}

}));

Route::get('icon/stop/{width?}', function($width = 16)
{
	$contents = View::make('svg-icon-stop')->with('width', $width);

	$response = Response::make($contents, 200);
	$response->header('Content-Type', 'image/svg+xml');

	return $response;
});

Route::get('icon/marker/{icon?}/{width?}', function($icon = 'train', $width = 16)
{
	$background_color = '1874CD';//231F20';

	$contents = View::make('svg-icon-marker')
	->with('icon', $icon)
	->with('background_color', $background_color)
	->with('width', $width);

	$response = Response::make($contents, 200);
	$response->header('Content-Type', 'image/svg+xml');
	$response->header('Content-Disposition', 'inline; filename="'.$icon.'-'.$width.'.svg"');

	return $response;
});

Route::get('stops/fix', function()
{

	$stops = Stop::with(array('coordinates' => function($query) {
		$schema = CSchema::find(1);
		$query->where('schema_id', $schema->id);
	}))->orderBy('name')->get();

	return View::make('stops-list')->with('stops', $stops);//->with('dim', $dim);
	
});

Route::get('stops/map', function()
{

	$stops = Stop::with(array('coordinates' => function($query) {
		$schema = CSchema::find(1);
		$query->where('schema_id', $schema->id);
	}))->orderBy('name')->get();

	$lines = Line::with(array('shapes' => function($query) {
		$schema = CSchema::find(1);
		$query->where('schema_id', $schema->id);
	}))->orderBy('name')->get();

	$dim = new MapDimensions(600, 800);
	$bounds = $dim->bounds();
	$size = $dim->size();

	return View::make('stops-svg')->with('stops', $stops)->with('lines', $lines)->with('dim', $dim);
	
});

Route::get('stops/kml', function()
{

	$stops = Stop::with(array('coordinates' => function($query) {
		$schema = CSchema::find(1);
		$query->where('schema_id', $schema->id);
	}))->orderBy('name')->get();

	$lines = Line::with(array('shapes' => function($query) {
		$schema = CSchema::find(1);
		$query->where('schema_id', $schema->id);
	}))->orderBy('name')->get();

	$contents = View::make('stops-kml')->with('stops', $stops)->with('lines', $lines);
	$response = Response::make($contents, 200);
	$response->header('Content-Type', 'application/vnd.google-earth.kml+xml');

	return $response;

});

Route::get('import', function() {
	echo Form::open(array('url' => 'import/stops', 'files' => true));

	echo Form::label('csv_file', 'CSV File');
	echo Form::file('csv_file');


	echo Form::submit('Upload');

	echo Form::close();
});

Route::post('import/stops', function() {

	if (Input::hasFile('csv_file'))
	{
		$inputFileName =Input::file('csv_file')->move(base_path('uploads'));
		
	}
	else {
		Redirect::to('import')->withInput();
		die();
	}


	echo '<pre>';
	$importFormat = false;

	try {
		$objReader = PHPExcel_IOFactory::createReader( $importFormat ? $importFormat : PHPExcel_IOFactory::identify($inputFileName) );
	}
	catch (Exception $e)  
	{  
		echo 'Invalid format set';
	} 
	
	
	$objReader->setReadDataOnly(true);
	
	try {
		$objPHPExcel = $objReader->load($inputFileName);
	}
	catch (Exception $e)  
	{  
		echo 'Invalid format for file';
	} 
	
	$tableData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	$tableHeaders = array_values(array_shift($tableData));

	$schema = CSchema::find(1);

	$startFrom = 64;

	foreach ($tableData as $key => &$tableRow) {
		if ($key >= $startFrom - 2) {
			$tableRow = array_combine($tableHeaders, $tableRow);

			$newStop = new Stop;
			$newStop->name = $tableRow['StationName'];
			$newStop->street = $tableRow['Address'];
			$newStop->city = $tableRow['City'];
			$newStop->save();

			$newCoords = new Coordinates;
			$newCoords->x = $tableRow['Longitude'];
			$newCoords->y = $tableRow['Latitude'];
			$newCoords->cschema()->associate($schema);
			$newCoords->stop()->associate($newStop);
			$newCoords->save();
		}
		// die();


	}

	// print_r($tableData);

});



// ----------------------------------------------------------- //

class MapDimensions {

	public $table;
	private $_bounds;
	private $_size;
	private $_canvas;

	function __construct($canvas_w, $canvas_h=false, $padding = 10) {
		$canvas = new stdClass;
		$canvas->width = $canvas_w;
		$canvas->height = $canvas_h ? $canvas_h : $canvas_w;
		$canvas->padding = $padding;
		$canvas->padded_width = $canvas->width + (2 * $padding);
		$canvas->padded_height = $canvas->height + (2 * $padding);
		$this->_canvas = $canvas;

		$this->table = DB::table('metroid_coordinates');
	}

	private function _calculate() {
		$bounds = $this->table->select(
			DB::raw('
				max(y) as north,
				min(y) as south,
				max(x) as east,
				min(x) as west
				')
			)->get();//->where('stop_id', '<', 200)->get();
		$bounds = $bounds[0];
		$this->_bounds = $bounds;

		$size = new stdClass;
		$size->height = abs($bounds->north - $bounds->south);
		$size->width = abs($bounds->west - $bounds->east);
		$this->_size = $size;
	}

	public function bounds() {
		if (!$this->_bounds) { $this->_calculate(); }
		return $this->_bounds;
	}
	public function size() {
		if (!$this->_size) { $this->_calculate(); }
		return $this->_size;
	}
	public function canvas() {
		return $this->_canvas;
	}

	public function convert($value, $y = false) {//, $dimension = 1) {

	$subtract = $y ? $this->_bounds->north : $this->_bounds->west;
	$divide = $y ? $this->_size->height : $this->_size->width;
	$dimension = $y ? $this->_canvas->height : $this->_canvas->width;

	return (abs(($value - $subtract) / $divide) * $dimension) + $this->_canvas->padding;
}

}