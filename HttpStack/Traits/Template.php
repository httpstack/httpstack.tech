<?php
namespace HttpStack\Traits;

trait Template{
        //this method will be moved into a trait
    public function setTitle(string $title): void {
        $this->title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        // Update the title in the DOM
        $titleElements = $this->xpath->query('//title');
        if ($titleElements->length > 0) {
            $titleElement = $titleElements->item(0);
            if ($titleElement instanceof DOMElement) {
                $titleElement->nodeValue = $this->title;
            }
        } else {
            // If no title element exists, create one
            $head = $this->xpath->query('//head')->item(0);
            if ($head instanceof DOMElement) {
                $newTitleElement = $this->dom->createElement('title', $this->title);
                $head->appendChild($newTitleElement);
            }
        }
    }
}
?>