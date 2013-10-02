<?php

/* tengchao@nyu.edu */


//This code is used in the context of building and fetching data from a tree that stores all the venues across the nation.


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

	public function getVenues($start, $end)

	{
		if($start > $end)

		{
			$bucket = $start;

			$start = $end;

			$end = $bucket;

		}
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


		$startDigits = array_splice($startDigits, $counter, 12);

		$endDigits = array_splice($endDigits, $counter , 12);

		$currentNode = $this->root;

		unset($fail);

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
		if(!isset($fail))

		{
			$diffDigitsLen = 13 - $counter;

			$firstDigitDiff = (int)$endDigits[0]-(int)$startDigits[0];

			$venueSet = array();

			if($firstDigitDiff > 1)

			{
				for($k = $startDigits[0] +1; $k <= $endDigits[0] -1; $k++)

				{
					$middleNode = $currentNode->child[$k];

					if(is_object($middleNode))

					{
						$venueSet = array_merge($venueSet, $middleNode->getVenues());

					}
				}


			}
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