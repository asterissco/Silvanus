<?php

/* SilvanusFirewallRulesBundle:FirewallRules:edit.html.twig */
class __TwigTemplate_9e39b8a081c4d35584fe92f20bd73b3f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("::base.html.twig");

        $this->blocks = array(
            'form_errors' => array($this, 'block_form_errors'),
            'form_row' => array($this, 'block_form_row'),
            'javascripts' => array($this, 'block_javascripts'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 6
        $this->env->getExtension('form')->renderer->setTheme((isset($context["edit_form"]) ? $context["edit_form"] : $this->getContext($context, "edit_form")), array(0 => $this));
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 9
    public function block_form_errors($context, array $blocks = array())
    {
        // line 10
        echo "    ";
        ob_start();
        // line 11
        echo "        ";
        if ((twig_length_filter($this->env, (isset($context["errors"]) ? $context["errors"] : $this->getContext($context, "errors"))) > 0)) {
            // line 12
            echo "
            ";
            // line 13
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["errors"]) ? $context["errors"] : $this->getContext($context, "errors")));
            foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                // line 14
                echo "                <div class=\"label label-danger\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "message"), "html", null, true);
                echo "</div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 16
            echo "        
        ";
        }
        // line 18
        echo "    ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 21
    public function block_form_row($context, array $blocks = array())
    {
        // line 22
        echo "    
    <div class=\"form-group\">
\t\t<div class=\"row\">\t
\t\t\t
\t\t\t<div class=\"col-md-12\">";
        // line 26
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'label');
        echo "</div>
\t\t\t<div class=\"col-md-8\">";
        // line 27
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'widget', array("attr" => array("class" => "form-control input-sm")));
        echo "</div>
\t\t\t<div class=\"col-md-4\">";
        // line 28
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'errors');
        echo "</div>
\t\t\t
\t\t</div>
    </div>
    
";
    }

    // line 35
    public function block_javascripts($context, array $blocks = array())
    {
        // line 36
        echo "
\t<script type=\"text/javascript\">
\t
\t\t\$(document).ready(function(){
\t\t
\t\t
\t\t});
\t
\t</script>

";
    }

    // line 49
    public function block_body($context, array $blocks = array())
    {
        // line 52
        echo "<div class=\"page-header\"><h1>Firewall <small>edit rule</small></h1></div>
\t\t\t
\t\t
\t\t\t<form action=\"";
        // line 55
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("firewallrules_update", array("id" => $this->getAttribute((isset($context["entity"]) ? $context["entity"] : $this->getContext($context, "entity")), "id"))), "html", null, true);
        echo "\" method=\"post\" ";
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["edit_form"]) ? $context["edit_form"] : $this->getContext($context, "edit_form")), 'enctype');
;
        echo " class=\"form-vertical\">
\t\t\t\t";
        // line 56
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["edit_form"]) ? $context["edit_form"] : $this->getContext($context, "edit_form")), 'widget');
        echo "
\t\t\t\t<p>
\t\t\t\t\t<button class=\"btn btn-success\" type=\"submit\">Update</button>
\t\t\t\t</p>
\t\t\t\t
\t\t\t
\t\t\t</form>
\t\t

\t\t<div class=\"col-md-12\">\t
\t\t\t\t<br><br>
\t\t\t\t
\t\t\t\t<div class=\"col-md-1\">\t
\t\t\t\t
\t\t\t\t\t<form action=\"";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("firewallrules_delete", array("id" => $this->getAttribute((isset($context["entity"]) ? $context["entity"] : $this->getContext($context, "entity")), "id"))), "html", null, true);
        echo "\" method=\"post\" class=\"form-inline\">
\t\t\t\t\t\t<input type=\"hidden\" name=\"_method\" value=\"DELETE\" />
\t\t\t\t\t\t";
        // line 72
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["delete_form"]) ? $context["delete_form"] : $this->getContext($context, "delete_form")), 'widget');
        echo "
\t\t\t\t\t\t<button type=\"submit\" class=\"btn btn-danger\" >Delete</button>
\t\t\t\t\t\t
\t\t\t\t\t</form>

\t\t\t\t</div>
\t\t\t\t<button class=\"btn btn-info\" onclick=\"window.location.href='";
        // line 78
        echo $this->env->getExtension('routing')->getPath("firewallrules");
        echo "'\">Back</button>
\t\t\t\t
\t\t\t
\t\t</div>
              

";
    }

    public function getTemplateName()
    {
        return "SilvanusFirewallRulesBundle:FirewallRules:edit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  148 => 72,  126 => 56,  97 => 36,  84 => 28,  76 => 26,  70 => 22,  58 => 16,  100 => 45,  81 => 38,  114 => 52,  90 => 52,  23 => 1,  77 => 33,  480 => 162,  474 => 161,  469 => 158,  461 => 155,  457 => 153,  453 => 151,  444 => 149,  440 => 148,  437 => 147,  435 => 146,  430 => 144,  427 => 143,  423 => 142,  413 => 134,  409 => 132,  407 => 131,  402 => 130,  398 => 129,  393 => 126,  387 => 122,  384 => 121,  381 => 120,  379 => 119,  374 => 116,  368 => 112,  365 => 111,  362 => 110,  360 => 109,  355 => 106,  341 => 105,  337 => 103,  322 => 101,  314 => 99,  312 => 98,  309 => 97,  305 => 95,  298 => 91,  294 => 90,  285 => 89,  283 => 88,  278 => 86,  268 => 85,  264 => 84,  258 => 81,  252 => 80,  247 => 78,  241 => 77,  229 => 73,  220 => 70,  214 => 69,  177 => 65,  169 => 60,  140 => 55,  132 => 51,  128 => 52,  107 => 36,  61 => 13,  273 => 96,  269 => 94,  254 => 92,  243 => 88,  240 => 86,  238 => 85,  235 => 74,  230 => 82,  227 => 81,  224 => 71,  221 => 77,  219 => 76,  217 => 75,  208 => 68,  204 => 72,  179 => 69,  159 => 61,  143 => 70,  135 => 53,  119 => 55,  102 => 32,  71 => 34,  67 => 21,  63 => 31,  59 => 14,  28 => 6,  201 => 92,  196 => 90,  183 => 82,  171 => 61,  166 => 71,  163 => 62,  158 => 67,  156 => 66,  151 => 63,  142 => 59,  138 => 54,  136 => 56,  121 => 46,  117 => 7,  105 => 40,  91 => 27,  62 => 18,  49 => 14,  94 => 35,  89 => 40,  85 => 39,  75 => 35,  68 => 14,  56 => 21,  38 => 6,  24 => 4,  26 => 6,  87 => 25,  31 => 4,  25 => 3,  21 => 2,  19 => 1,  93 => 28,  88 => 6,  78 => 21,  46 => 16,  44 => 12,  27 => 4,  79 => 18,  72 => 37,  69 => 25,  47 => 9,  40 => 7,  37 => 10,  22 => 2,  246 => 90,  157 => 78,  145 => 46,  139 => 45,  131 => 52,  123 => 47,  120 => 40,  115 => 43,  111 => 49,  108 => 5,  101 => 32,  98 => 56,  96 => 55,  83 => 25,  74 => 14,  66 => 24,  55 => 15,  52 => 21,  50 => 10,  43 => 6,  41 => 5,  35 => 10,  32 => 4,  29 => 3,  209 => 82,  203 => 78,  199 => 67,  193 => 73,  189 => 71,  187 => 84,  182 => 66,  176 => 64,  173 => 65,  168 => 72,  164 => 59,  162 => 57,  154 => 58,  149 => 51,  147 => 58,  144 => 49,  141 => 48,  133 => 55,  130 => 41,  125 => 44,  122 => 8,  116 => 41,  112 => 42,  109 => 34,  106 => 49,  103 => 32,  99 => 31,  95 => 28,  92 => 53,  86 => 28,  82 => 22,  80 => 27,  73 => 19,  64 => 26,  60 => 6,  57 => 11,  54 => 10,  51 => 14,  48 => 13,  45 => 13,  42 => 12,  39 => 11,  36 => 10,  33 => 9,  30 => 7,);
    }
}
