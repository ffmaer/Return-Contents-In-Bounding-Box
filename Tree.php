<?php

/* tengchao@nyu.edu */



class Tree

{
	private $root;

	public function __construct()

	{
		$this->root = new Node();

	}

/********************************************************************

               addVenue


This procedure puts venue names and ids at specific locations in a tree 
structure according to given coordinates.


coordinate: 13 digit normalized coordinate. This coordinate is used as

			the address to store a node.


id:			the unique number attached to a venue.

venue:  	the name of a venue.

********************************************************************/

	public function addVenue($coordinate, $id, $venue)

	{
		$coordinateDigits = str_split($coordinate);

		$currentNode = $this->root;


		foreach($coordinateDigits as $v)

		{
			if(is_object($currentNode->child[(int)$v]))

			{
				$tempNode = $currentNode->child[(int)$v];

			}
			else

			{
				$tempNode = new Node();	

			}
			$tempNode->addVenue($id, $venue);

			$currentNode->child[(int)$v] = $tempNode;

			$currentNode = $currentNode->child[(int)$v];

		}
	}
/********************************************************************

               printTree


This procedure prints the tree that stores the venues in the form of array
********************************************************************/

	public function printTree()

	{
		print_r($this->root);

	}
/********************************************************************

               getVenue


This procedure fetches all venues between two latitudes or two longitudes.


start:		13 digit normalized coordinate. This is the starting point.


end:		13 digit normalized coordinate. This is the ending point.
********************************************************************/

	// start and end parameters are not required
	public function getVenues($start, $end)

	{
		// make sure $end is always larger than $start
		if($start > $end)

		{
			$bucket = $start;

			$start = $end;

			$end = $bucket;

		}
		// the following code tries to find the first different digit
		// 12235 vs 12245 --> the first different digit is 3 vs 4
		// 122~35 vs 122~45 --> the 122 parts are the same
		// return 3 and 4
		$startDigits = str_split($start);

		$endDigits = str_split($end);

		$sameDigits = array();

		$i = 0;

		$counter = 0;

		while($i < 13)

		{
			if($startDigits[$i] == $endDigits[$i])

			{

				array_push($sameDigits, $startDigits[$i]);

				$counter++;

			}
			else
			{

				break;
			}

			$i++;
		}

		// startDigits --> 35 endDigits --> 45
		$startDigits = array_splice($startDigits, $counter, 12);

		$endDigits = array_splice($endDigits, $counter , 12);

		
		$currentNode = $this->root;

		// fail means the path does not exist
		$fail = 0;

		foreach($sameDigits as $v)

		{
			if(is_object($currentNode->child[(int)$v]))

			{
				$currentNode = $currentNode->child[(int)$v];

			}
			else

			{
				$fail = 1;

				break;
			}


		}
		if(0 == $fail)

		{

			// in the case of 122~35 vs 122~45, the diff is 2
			$diffDigitsLen = 13 - $counter;

			// 4 - 3 = 1, it is guaranteed positive
			$firstDigitDiff = (int)$endDigits[0]-(int)$startDigits[0];

			$venueSet = array();


			// when they are not next to each other
			// deal with the points in between
			if($firstDigitDiff > 1)

			{
				// the digits in between
				// for example the digits between 3 and 7 are 4,5,6
				for($k = $startDigits[0] +1; $k <= $endDigits[0] -1; $k++)

				{ 
					$middleNode = $currentNode->child[$k];

					if(is_object($middleNode))

					{
						// recursion
						$venueSet = array_merge($venueSet, $middleNode->getVenues());

					}
				}
			}


			// deal with the right end point
			if(is_object($currentNode->child[$startDigits[0]]))

			{
				$leftNode = $currentNode->child[$startDigits[0]];

				for ($j = 1; $j < $diffDigitsLen - 1; $j++)

				{
					for($k = $startDigits[$j] + 1; $k <= 9; $k++)

					{
						if(is_object($leftNode->child[$k]))

						{
							$venueSet = array_merge($venueSet, $leftNode->child[$k]->getVenues());

						}
					}

					if(is_object($leftNode->child[$startDigits[$j]]))

					{
						$leftNode = $leftNode->child[$startDigits[$j]];

					}
				}
				if(is_object($leftNode))

				{
					$venueSet = array_merge($venueSet, $leftNode->getVenues());

				}
			}



			// deal with the right end point
			if(is_object($currentNode->child[$endDigits[0]]))

			{
				$rightNode = $currentNode->child[$endDigits[0]];

				for ($j = 1; $j < $diffDigitsLen - 1; $j++)

				{
					for($k = 0; $k <= $endDigits[$j] - 1; $k++)

					{
						if(is_object($rightNode->child[$k]))

						{
							$venueSet = array_merge($venueSet, $rightNode->child[$k]->getVenues());

						}
					}

					if(is_object($rightNode->child[$endDigits[$j]]))

					{
						$rightNode = $rightNode->child[$startDigits[$j]];

					}
				}
				if(is_object($rightNode))

				{
					$venueSet = array_merge($venueSet, $rightNode->getVenues());

				}
			}
		}

		return $venueSet;

	}
}