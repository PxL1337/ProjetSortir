<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeTransformer implements DataTransformerInterface
{
    public function transform($dateInterval)
    {
        if (null === $dateInterval) {
            return new \DateTime();
        }

        $time = sprintf('%02d:%02d', $dateInterval->h, $dateInterval->i);
        return \DateTime::createFromFormat('H:i', $time);
    }

    public function reverseTransform($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        $time = $dateTime->format('H:i');
        list($hours, $minutes) = explode(':', $time);
        return new \DateInterval(sprintf('PT%dH%dM', $hours, $minutes));
    }
}