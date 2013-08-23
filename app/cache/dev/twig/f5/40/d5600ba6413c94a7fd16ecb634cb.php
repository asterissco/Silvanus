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
        // line 10
        echo "        <link rel=\"icon\" type=\"image/x-icon\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />


\t\t\t<script type=\"text/javascript\" src=\"";
        // line 13
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/silvanus/js/jquery-2.0.3.min.js"), "html", null, true);
        echo "\"></script>
\t\t\t<script type=\"text/javascript\" src=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/silvanus/js/bootstrap.min.js"), "html", null, true);
        echo "\"></script>\t\t
    
\t\t\t<script type=\"text/javascript\">
\t\t\t
\t\t\t
\t\t\t</script>
    
    </head>
    <body>

\t<div class=\"navbar navbar-inverse navbar-fixed-top\">
\t\t<div class=\"container\">
\t\t\t<div class=\"navbar-header\">
\t\t\t\t<button class=\"navbar-toggle\" data-target=\".navbar-collapse\" data-toggle=\"collapse\" type=\"button\">
\t\t\t\t<span class=\"icon-bar\"></span>
\t\t\t\t<span class=\"icon-bar\"></span>
\t\t\t\t<span class=\"icon-bar\"></span>
\t\t\t\t</button>
\t\t\t\t<a class=\"navbar-brand\" href=\"#\">Silvanus</a>
\t\t\t</div>
\t\t\t<div class=\"collapse navbar-collapse\">
\t\t\t\t<ul class=\"nav navbar-nav\">
\t\t\t\t\t<li class=\"active\">
\t\t\t\t\t<a href=\"";
        // line 37
        echo $this->env->getExtension('routing')->getPath("firewallrules");
        echo "\">Firewall Rules</a>
\t\t\t\t</li>
\t\t\t\t<li>
\t\t\t\t\t<a href=\"#about\">About</a>
\t\t\t\t</li>
\t\t\t\t<li>
\t\t\t\t\t<a href=\"#contact\">Contact</a>
\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>
\t</div>        
\t
\t
\t<div class=\"container\">\t
\t\t";
        // line 52
        $this->displayBlock('body', $context, $blocks);
        // line 53
        echo "\t\t</div>\t
\t
\t\t";
        // line 55
        $this->displayBlock('javascripts', $context, $blocks);
        // line 56
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
\t\t\t<link rel=\"STYLESHEET\" type=\"text/css\" href=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/silvanus/css/silvanus.css"), "html", null, true);
        echo "\"></link>
        ";
    }

    // line 52
    public function block_body($context, array $blocks = array())
    {
    }

    // line 55
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
        return array (  133 => 55,  128 => 52,  122 => 8,  117 => 7,  114 => 6,  108 => 5,  98 => 56,  96 => 55,  92 => 53,  90 => 52,  72 => 37,  46 => 14,  35 => 10,  29 => 5,  23 => 1,  158 => 79,  146 => 70,  139 => 69,  135 => 67,  132 => 65,  97 => 32,  94 => 31,  84 => 24,  80 => 23,  76 => 22,  70 => 18,  67 => 17,  62 => 14,  58 => 12,  49 => 10,  45 => 9,  42 => 13,  39 => 7,  36 => 6,  33 => 6,  28 => 3,);
    }
}
