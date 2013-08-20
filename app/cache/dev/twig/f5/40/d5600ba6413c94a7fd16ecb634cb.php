<?php

/* ::base.html.twig */
class __TwigTemplate_f540d5600ba6413c94a7fd16ecb634cb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'content' => array($this, 'block_content'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
        ";
        // line 6
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 9
        echo "        <link rel=\"icon\" type=\"image/x-icon\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />


\t\t\t<script type=\"text/javascript\" src=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/silvanus/js/jquery-2.0.3.min.js"), "html", null, true);
        echo "\"></script>
\t\t\t<script type=\"text/javascript\" src=\"";
        // line 13
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/silvanus/js/bootstrap.min.js"), "html", null, true);
        echo "\"></script>\t\t

\t\t
    </head>
    <body>
        
        ";
        // line 19
        $this->displayBlock('body', $context, $blocks);
        // line 20
        echo "
\t\t";
        // line 21
        $this->displayBlock('content', $context, $blocks);
        // line 22
        echo "
\t\t";
        // line 23
        $this->displayBlock('javascripts', $context, $blocks);
        // line 24
        echo "\t\t
\t\t
\t\t
\t\t
    </body>
</html>
";
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        echo "Welcome!";
    }

    // line 6
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 7
        echo "\t\t\t<link rel=\"STYLESHEET\" type=\"text/css\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/silvanus/css/bootstrap.min.css"), "html", null, true);
        echo "\"></link> 
        ";
    }

    // line 19
    public function block_body($context, array $blocks = array())
    {
    }

    // line 21
    public function block_content($context, array $blocks = array())
    {
        echo " ";
    }

    // line 23
    public function block_javascripts($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "::base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  105 => 23,  99 => 21,  94 => 19,  87 => 7,  84 => 6,  78 => 5,  68 => 24,  66 => 23,  63 => 22,  61 => 21,  58 => 20,  56 => 19,  47 => 13,  43 => 12,  36 => 9,  34 => 6,  30 => 5,  24 => 1,);
    }
}
