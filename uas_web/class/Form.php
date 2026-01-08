<?php 
class Form { 
    private $fields = []; 
    private $action; 
    private $submit = "Simpan Data"; 

    public function __construct($action, $submit) { 
        $this->action = $action; 
        $this->submit = $submit; 
    } 

    public function displayForm() { 
        echo "<div class='card shadow-sm border-0 mb-4'><div class='card-body p-4'>";
        echo "<form action='{$this->action}' method='POST'>"; 
        
        foreach ($this->fields as $field) { 
            echo "<div class='mb-3'>";
            echo "<label class='form-label fw-bold text-muted small'>" . $field['label'] . "</label>"; 
            
            // Logika menampilkan value (jika ada)
            $val = isset($field['value']) ? htmlspecialchars($field['value']) : "";

            if ($field['type'] == 'textarea') {
                echo "<textarea name='{$field['name']}' class='form-control' rows='5' style='background:#f8f9fa; border:1px solid #ddd'>{$val}</textarea>"; 
            } else {
                echo "<input type='{$field['type']}' name='{$field['name']}' value='{$val}' class='form-control' style='background:#f8f9fa; border:1px solid #ddd'>"; 
            }
            echo "</div>"; 
        } 

        echo "<div class='mt-4 d-flex gap-2'>";
        echo "<button type='submit' class='btn btn-primary px-4 py-2'><i class='fa-solid fa-save me-2'></i>{$this->submit}</button>"; 
        // Tombol Batal
        echo "<a href='/uas_web/index.php/artikel/index' class='btn btn-light px-4 py-2 border'>Batal</a>";
        echo "</div>"; 
        
        echo "</form></div></div>"; 
    } 

    public function addField($name, $label, $type = "text", $value = "") { 
        $this->fields[] = [
            'name' => $name, 
            'label' => $label, 
            'type' => $type,
            'value' => $value 
        ]; 
    } 
} 
?>