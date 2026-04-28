<?php
/*
    File: destination.class.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Defines the Destination class used by destinations.php
                 to represent a geographic destination region (e.g. Caribbean,
                 Europe). Encapsulates the display name, icon, description,
                 anchor ID, alt text, background colour, and a list of featured
                 spots. Provides helper methods used in the page template.
*/

class Destination {
    public string $name;
    public string $icon;
    public string $description;
    public string $anchor;
    public string $altText;
    public string $bgColor;
    private array $spots;

    public function __construct(
        string $name, string $icon, string $description,
        string $anchor, string $altText, array $spots, string $bgColor = ""
    ) {
        $this->name = $name; $this->icon = $icon; $this->description = $description;
        $this->anchor = $anchor; $this->altText = $altText;
        $this->spots = $spots; $this->bgColor = $bgColor;
    }

    public function getHeaderTitle(): string { return $this->icon . " " . $this->name; }
    public function getSpotsCount(): int { return count($this->spots); }

    public function renderSpots(): string {
        $html = '<div class="service-details">';
        foreach ($this->spots as $spot) {
            $html .= '<div class="service-feature"><h3>' . htmlspecialchars($spot['name'])
                  . '</h3><p>' . htmlspecialchars($spot['desc']) . '</p></div>';
        }
        return $html . '</div>';
    }
}
