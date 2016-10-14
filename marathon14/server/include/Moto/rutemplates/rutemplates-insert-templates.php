<?php
if (!isset($_GET['ADDRUTEMPLATES'])){
 return;
}


header('x-debug-ru:run');

define("ENABLE_TABLE_INSERT", 1);
$VERBOSE = 0;
if (isset($_GET['VERBOSE']))
	$VERBOSE = $_GET['VERBOSE'];
define("VERBOSE", $VERBOSE);

$idsToRecover = array(42441, 42442, 42443, 42444, 42445, 42446, 42447, 42448, 42449, 42450, 48532, 48534, 48387, 48533, 48711, 48712, 48713, 48714, 48715, 48716, 48721, 50575, 50576, 50577, 50578, 50579, 50580, 50581, 50582, 50583, 50584, 50841, 50842, 50843, 50844, 50845, 50846, );
$templateTypeId = 68;

include ('Moto/rutemplates/classes.php');
$templates = new Rutemplates_Templates($templateTypeId);


if ((ENABLE_TABLE_INSERT && !$templates->needUpdate($templateTypeId, $idsToRecover)) || 1 == 1 )
{
    if (VERBOSE)
    {
        echo "<p>Template type # $templateTypeId and templates already exist</p>";
    }
    //return;
}

if (strpos($_SERVER['HTTP_HOST'], '.fmt') === false)
	@mail('pirojok2004@gmail.com', 'TM.RU :: ru templates start work ' . date('Y-m-d H:i:s'), print_r($_SERVER, true));

// fill templates data
$_templates = array();
$_previews = array();

$_templates[] = array(
    'main' => array(
        'id' => 42441,
        'templateauthor_id' => 161
    ),
    'additional' => array(
        'categories' => array(40)
    ),
    'smallPreview' => array(
        'uri' => '42400/42441-m.jpg',
        'width' => 145,
        'height' => 123
    ),
    'largePreview' => array(
        'uri' => '42400/42441-b.jpg',
        'width' => 430,
        'height' => 729
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42442,
        'templateauthor_id' => 232
    ),
    'additional' => array(
        'categories' => array(2)
    ),
    'smallPreview' => array(
        'uri' => '42400/42442-m.jpg',
        'width' => 145,
        'height' => 135
    ),
    'largePreview' => array(
        'uri' => '42400/42442-b.jpg',
        'width' => 430,
        'height' => 801
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42443,
        'templateauthor_id' => 210
    ),
    'additional' => array(
        'categories' => array(2)
    ),
    'smallPreview' => array(
        'uri' => '42400/42443-m.jpg',
        'width' => 145,
        'height' => 156
    ),
    'largePreview' => array(
        'uri' => '42400/42443-b.jpg',
        'width' => 430,
        'height' => 727
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42444,
        'templateauthor_id' => 232
    ),
    'additional' => array(
        'categories' => array(2)
    ),
    'smallPreview' => array(
        'uri' => '42400/42444-m.jpg',
        'width' => 145,
        'height' => 156
    ),
    'largePreview' => array(
        'uri' => '42400/42444-b.jpg',
        'width' => 430,
        'height' => 664
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42445,
        'templateauthor_id' => 232
    ),
    'additional' => array(
        'categories' => array(40)
    ),
    'smallPreview' => array(
        'uri' => '42400/42445-m.jpg',
        'width' => 145,
        'height' => 115
    ),
    'largePreview' => array(
        'uri' => '42400/42445-b.jpg',
        'width' => 430,
        'height' => 685
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42446,
        'templateauthor_id' => 210
    ),
    'additional' => array(
        'categories' => array(24)
    ),
    'smallPreview' => array(
        'uri' => '42400/42446-m.jpg',
        'width' => 145,
        'height' => 156
    ),
    'largePreview' => array(
        'uri' => '42400/42446-b.jpg',
        'width' => 430,
        'height' => 719
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42447,
        'templateauthor_id' => 145
    ),
    'additional' => array(
        'categories' => array(2)
    ),
    'smallPreview' => array(
        'uri' => '42400/42447-m.jpg',
        'width' => 145,
        'height' => 156
    ),
    'largePreview' => array(
        'uri' => '42400/42447-b.jpg',
        'width' => 430,
        'height' => 691
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42448,
        'templateauthor_id' => 210
    ),
    'additional' => array(
        'categories' => array(46)
    ),
    'smallPreview' => array(
        'uri' => '42400/42448-m.jpg',
        'width' => 145,
        'height' => 156
    ),
    'largePreview' => array(
        'uri' => '42400/42448-b.jpg',
        'width' => 430,
        'height' => 497
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42449,
        'templateauthor_id' => 161
    ),
    'additional' => array(
        'categories' => array(76)
    ),
    'smallPreview' => array(
        'uri' => '42400/42449-m.jpg',
        'width' => 145,
        'height' => 122
    ),
    'largePreview' => array(
        'uri' => '42400/42449-b.jpg',
        'width' => 430,
        'height' => 725
    )
);

$_templates[] = array(
    'main' => array(
        'id' => 42450,
        'templateauthor_id' => 145
    ),
    'additional' => array(
        'categories' => array(10)
    ),
    'smallPreview' => array(
        'uri' => '42400/42450-m.jpg',
        'width' => 145,
        'height' => 156
    ),
    'largePreview' => array(
        'uri' => '42400/42450-b.jpg',
        'width' => 430,
        'height' => 549
    )
);
/**new from march 2013**/

$_templates[] = array(
	'main' => array(
		'id' => 48532,
		'templateauthor_id' => 161
	),
	'additional' => array(
		'categories' => array(27)
	),
	'smallPreview' => array(
		'uri' => '48500/48532-m.jpg',
		'width' => 145,
		'height' => 110
	),
	'largePreview' => array(
		'uri' => '48500/48532-b.jpg',
		'width' => 430,
		'height' => 685
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 48534,
		'templateauthor_id' => 232
	),
	'additional' => array(
		'categories' => array(26)
	),
	'smallPreview' => array(
		'uri' => '48500/48534-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '48500/48534-b.jpg',
		'width' => 430,
		'height' => 612
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 48387,
		'templateauthor_id' => 85
	),
	'additional' => array(
		'categories' => array(1)
	),
	'smallPreview' => array(
		'uri' => '48300/48387-m.jpg',
		'width' => 145,
		'height' => 110
	),
	'largePreview' => array(
		'uri' => '48300/48387-b.jpg',
		'width' => 430,
		'height' => 612
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 48711,
		'templateauthor_id' => 85
	),
	'additional' => array(
		'categories' => array(24)
	),
	'smallPreview' => array(
		'uri' => '48700/48711-m.jpg',
		'width' => 145,
		'height' => 120
	),
	'largePreview' => array(
		'uri' => '48700/48711-b.jpg',
		'width' => 430,
		'height' => 680
	)
);
$_templates[] = array(
	'main' => array(
		'id' => 48712,
		'templateauthor_id' => 232
	),
	'additional' => array(
		'categories' => array(17)
	),
	'smallPreview' => array(
		'uri' => '48700/48712-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '48700/48712-b.jpg',
		'width' => 430,
		'height' => 609
	)
);
$_templates[] = array(
	'main' => array(
		'id' => 48713,
		'templateauthor_id' => 248
	),
	'additional' => array(
		'categories' => array(49)
	),
	'smallPreview' => array(
		'uri' => '48700/48713-m.jpg',
		'width' => 145,
		'height' => 120
	),
	'largePreview' => array(
		'uri' => '48700/48713-b.jpg',
		'width' => 430,
		'height' => 707
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 48714,
		'templateauthor_id' => 210
	),
	'additional' => array(
		'categories' => array(11)
	),
	'smallPreview' => array(
		'uri' => '48700/48714-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '48700/48714-b.jpg',
		'width' => 430,
		'height' => 593
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 48715,
		'templateauthor_id' => 210
	),
	'additional' => array(
		'categories' => array(118)
	),
	'smallPreview' => array(
		'uri' => '48700/48715-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '48700/48715-b.jpg',
		'width' => 430,
		'height' => 483
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 48716,
		'templateauthor_id' => 257
	),
	'additional' => array(
		'categories' => array(2)
	),
	'smallPreview' => array(
		'uri' => '48700/48716-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '48700/48716-b.jpg',
		'width' => 430,
		'height' => 548
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 48721,
		'templateauthor_id' => 161
	),
	'additional' => array(
		'categories' => array(118)
	),
	'smallPreview' => array(
		'uri' => '48700/48721-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '48700/48721-b.jpg',
		'width' => 430,
		'height' => 483
	)
);

/**new from July 2014**/

$_templates[] = array(
	'main' => array(
		'id' => 50575,
		'templateauthor_id' => 179
	),
	'additional' => array(
		'categories' => array(46)
	),
	'smallPreview' => array(
		'uri' => '50500/50575-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50500/50575-b.jpg',
		'width' => 430,
		'height' => 645
	)
);


$_templates[] = array(
	'main' => array(
		'id' => 50576,
		'templateauthor_id' => 113
	),
	'additional' => array(
		'categories' => array(1)
	),
	'smallPreview' => array(
		'uri' => '50500/50576-m.jpg',
		'width' => 145,
		'height' => 110
	),
	'largePreview' => array(
		'uri' => '50500/50576-b.jpg',
		'width' => 430,
		'height' => 765
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50577,
		'templateauthor_id' => 232
	),
	'additional' => array(
		'categories' => array(1)
	),
	'smallPreview' => array(
		'uri' => '50500/50577-m.jpg',
		'width' => 145,
		'height' => 130
	),
	'largePreview' => array(
		'uri' => '50500/50577-b.jpg',
		'width' => 430,
		'height' => 387
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50578,
		'templateauthor_id' => 183
	),
	'additional' => array(
		'categories' => array(23)
	),
	'smallPreview' => array(
		'uri' => '50500/50578-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50500/50578-b.jpg',
		'width' => 430,
		'height' => 519
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50579,
		'templateauthor_id' => 73
	),
	'additional' => array(
		'categories' => array(23)
	),
	'smallPreview' => array(
		'uri' => '50500/50579-m.jpg',
		'width' => 145,
		'height' => 155
	),
	'largePreview' => array(
		'uri' => '50500/50579-b.jpg',
		'width' => 430,
		'height' => 461
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50580,
		'templateauthor_id' => 311
	),
	'additional' => array(
		'categories' => array(24)
	),
	'smallPreview' => array(
		'uri' => '50500/50580-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50500/50580-b.jpg',
		'width' => 430,
		'height' => 645
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50581,
		'templateauthor_id' => 128
	),
	'additional' => array(
		'categories' => array(1)
	),
	'smallPreview' => array(
		'uri' => '50500/50581-m.jpg',
		'width' => 145,
		'height' => 110
	),
	'largePreview' => array(
		'uri' => '50500/50581-b.jpg',
		'width' => 430,
		'height' => 912
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50582,
		'templateauthor_id' => 128
	),
	'additional' => array(
		'categories' => array(43)
	),
	'smallPreview' => array(
		'uri' => '50500/50582-m.jpg',
		'width' => 145,
		'height' => 110
	),
	'largePreview' => array(
		'uri' => '50500/50582-b.jpg',
		'width' => 430,
		'height' => 710
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50583,
		'templateauthor_id' => 231
	),
	'additional' => array(
		'categories' => array(34)
	),
	'smallPreview' => array(
		'uri' => '50500/50583-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50500/50583-b.jpg',
		'width' => 430,
		'height' => 664
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50584,
		'templateauthor_id' => 231
	),
	'additional' => array(
		'categories' => array(46)
	),
	'smallPreview' => array(
		'uri' => '50500/50584-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50500/50584-b.jpg',
		'width' => 430,
		'height' => 537
	)
);

/**new from August 2014**/

$_templates[] = array(
	'main' => array(
		'id' => 50844,
		'templateauthor_id' => 316
	),
	'additional' => array(
		'categories' => array(2)
	),
	'smallPreview' => array(
		'uri' => '50800/50844-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50800/50844-b.jpg',
		'width' => 430,
		'height' => 1000
	)
);

$_templates[] = array(
	'main' => array(
		'id' => 50845,
		'templateauthor_id' => 316
	),
	'additional' => array(
		'categories' => array(2)
	),
	'smallPreview' => array(
		'uri' => '50800/50845-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50800/50845-b.jpg',
		'width' => 430,
		'height' => 995
	)
);
$_templates[] = array(
	'main' => array(
		'id' => 50846,
		'templateauthor_id' => 316
	),
	'additional' => array(
		'categories' => array(2)
	),
	'smallPreview' => array(
		'uri' => '50800/50846-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50800/50846-b.jpg',
		'width' => 430,
		'height' => 906
	)
);
$_templates[] = array(
	'main' => array(
		'id' => 50841,
		'templateauthor_id' => 316
	),
	'additional' => array(
		'categories' => array(2)
	),
	'smallPreview' => array(
		'uri' => '50800/50841-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50800/50841-b.jpg',
		'width' => 430,
		'height' => 1000
	)
);
$_templates[] = array(
	'main' => array(
		'id' => 50842,
		'templateauthor_id' => 316
	),
	'additional' => array(
		'categories' => array(2)
	),
	'smallPreview' => array(
		'uri' => '50800/50842-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50800/50842-b.jpg',
		'width' => 430,
		'height' => 1000
	)
);
$_templates[] = array(
	'main' => array(
		'id' => 50843,
		'templateauthor_id' => 316
	),
	'additional' => array(
		'categories' => array(2)
	),
	'smallPreview' => array(
		'uri' => '50800/50843-m.jpg',
		'width' => 145,
		'height' => 156
	),
	'largePreview' => array(
		'uri' => '50800/50843-b.jpg',
		'width' => 430,
		'height' => 1000
	)
);



$templates->addTemplates($_templates);


if (ENABLE_TABLE_INSERT)
{
    $templates->run();
}
else
{
    //$templates->recover($idsToRecover);
}
if (strpos($_SERVER['HTTP_HOST'], '.fmt') === false)
	@mail('pirojok2004@gmail.com', 'TM.RU :: ru templates finish work ' . date('Y-m-d H:i:s'), print_r($_SERVER, true));
