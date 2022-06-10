<?php

class ServiceClass
{
    public function _testFunction($dateTime)
    {
        $dateTime->add('issue_date', DateType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'required' => true,
            'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            'data' => new \DateTime(),
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\DateTime(),
            ]]);
    }
    
    
    public function _unrelatedTypeFunction($oooooType)
    {
        $oooooType->add('issue_date', OOOOType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'required' => true,
            'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            'data' => new \DateTime(),
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\DateTime(),
            ]]);
    }
    
    public function _unrelatedFunctionParameter($xxxx)
    {
        $xxxx->somethingUnrelated([
            'format' => 'yyyy-MM-dd',
            'label' => 'OK'
        ]);
    }
}
