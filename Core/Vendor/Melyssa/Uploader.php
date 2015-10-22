<?php
namespace Melyssa;

class Uploader
{
    protected $photo = array();

    public function setPhoto($file, $folder, $prefix, $suffix, $width)
    {
        $this->photo['string'] = $file;
        $this->photo['folder'] = $folder;
        $this->photo['name'] = $prefix . md5(uniqid(rand(), true)) . $suffix . '.jpg';
        $this->photo['width'] = $width;
    }

    private function createJpeg($waterMark = false, $wMFile = '')
    {
        $image = imagecreatefromstring(base64_decode($this->photo['string']));
        $x = imagesx($image);
        $y = imagesy($image);
        $height = ($this->photo['width'] * $y) / $x;
        $newimage = imagecreatetruecolor($this->photo['width'], $height);
        imagecopyresampled($newimage, $image, 0, 0, 0, 0, $this->photo['width'], $height, $x, $y);
        $folder = $this->photo['folder'];
        $name = $this->photo['name'];
        
        if(true === $waterMark){
            // Criando a imagem da logomarca:
            $logo = imagecreatefrompng($wMFile);
            $size = getimagesize($wMFile);
            // Pegando altura e largura da logomarca:
            $logoW = $size[0];
            $logoH = $size[1];
            // Calculando a posição da logomarca conforme o tamanho da imagem enviada:
            $posYtoLogo = $height - ($logoH + 50);
            $posXToLogo = $this->photo['width'] / 2 - ($logoW / 2);
            // Copiando a logomarca pra dentro da imagem final:
            imagecopyresampled($newimage, $logo, $posXToLogo, $posYtoLogo, 0, 0, $logoW, $logoH, $logoW, $logoH);
        }
        
        imagejpeg($newimage, "$folder/$name");
        imagedestroy($newimage);
        imagedestroy($image);
    }

    public function doUpload($waterMark = false, $wmFile = '')
    {
        // Verificando poss�veis erros:
        $this->createJpeg($waterMark, $wmFile);
        // Retornando o nome da imagem para cadastro no banco de dados ou outra fun��o:
        return $this->photo['name'];
    }
}