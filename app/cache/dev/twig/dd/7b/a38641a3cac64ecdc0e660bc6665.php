<?php

/* SilvanusFirewallRulesBundle:FirewallRules:index.html.twig */
class __TwigTemplate_dd7ba38641a3cac64ecdc0e660bc6665 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("::base.html.twig");

        $this->blocks = array(
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
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_javascripts($context, array $blocks = array())
    {
        // line 4
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

    // line 16
    public function block_body($context, array $blocks = array())
    {
        // line 19
        echo "<div class=\"page-header\"><h1>Firewall <small>rule list</small></h1></div>

    <table class=\"table\">
        <thead>
            <tr>
                
                <th>Priority</th>
                <th>Rule</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        ";
        // line 31
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["entities"]) ? $context["entities"] : $this->getContext($context, "entities")));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 32
            echo "            <tr>
                
                <td>";
            // line 34
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["entity"]) ? $context["entity"] : $this->getContext($context, "entity")), "priority"), "html", null, true);
            echo "</td>
                <td>";
            // line 35
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["entity"]) ? $context["entity"] : $this->getContext($context, "entity")), "rule"), "html", null, true);
            echo "</td>
                <td>
                
                        <button class=\"btn btn-info\" onclick=\"window.location.href='";
            // line 38
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("firewallrules_show", array("id" => $this->getAttribute((isset($context["entity"]) ? $context["entity"] : $this->getContext($context, "entity")), "id"))), "html", null, true);
            echo "'\">Show</button>
                        <button class=\"btn btn-warning\"  onclick=\"window.location.href='";
            // line 39
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("firewallrules_edit", array("id" => $this->getAttribute((isset($context["entity"]) ? $context["entity"] : $this->getContext($context, "entity")), "id"))), "html", null, true);
            echo "'\">Edit</button>
                        <button class=\"btn btn-danger\"  onclick=\"window.location.href='";
            // line 40
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("firewallrules_delete", array("id" => $this->getAttribute((isset($context["entity"]) ? $context["entity"] : $this->getContext($context, "entity")), "id"))), "html", null, true);
            echo "'\">Delete</button>

                </td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 45
        echo "        </tbody>
    </table>

\t\t\t
            <button class=\"btn btn-success\" onclick=\"window.location.href='";
        // line 49
        echo $this->env->getExtension('routing')->getPath("firewallrules_new");
        echo "'\">New rule</button>

";
    }

    public function getTemplateName()
    {
        return "SilvanusFirewallRulesBundle:FirewallRules:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  100 => 45,  81 => 38,  114 => 6,  90 => 52,  23 => 1,  77 => 33,  480 => 162,  474 => 161,  469 => 158,  461 => 155,  457 => 153,  453 => 151,  444 => 149,  440 => 148,  437 => 147,  435 => 146,  430 => 144,  427 => 143,  423 => 142,  413 => 134,  409 => 132,  407 => 131,  402 => 130,  398 => 129,  393 => 126,  387 => 122,  384 => 121,  381 => 120,  379 => 119,  374 => 116,  368 => 112,  365 => 111,  362 => 110,  360 => 109,  355 => 106,  341 => 105,  337 => 103,  322 => 101,  314 => 99,  312 => 98,  309 => 97,  305 => 95,  298 => 91,  294 => 90,  285 => 89,  283 => 88,  278 => 86,  268 => 85,  264 => 84,  258 => 81,  252 => 80,  247 => 78,  241 => 77,  229 => 73,  220 => 70,  214 => 69,  177 => 65,  169 => 60,  140 => 55,  132 => 51,  128 => 52,  107 => 36,  61 => 13,  273 => 96,  269 => 94,  254 => 92,  243 => 88,  240 => 86,  238 => 85,  235 => 74,  230 => 82,  227 => 81,  224 => 71,  221 => 77,  219 => 76,  217 => 75,  208 => 68,  204 => 72,  179 => 69,  159 => 61,  143 => 56,  135 => 53,  119 => 42,  102 => 32,  71 => 34,  67 => 32,  63 => 31,  59 => 14,  28 => 3,  201 => 92,  196 => 90,  183 => 82,  171 => 61,  166 => 71,  163 => 62,  158 => 67,  156 => 66,  151 => 63,  142 => 59,  138 => 54,  136 => 56,  121 => 46,  117 => 7,  105 => 40,  91 => 27,  62 => 23,  49 => 19,  94 => 28,  89 => 40,  85 => 39,  75 => 35,  68 => 14,  56 => 21,  38 => 6,  24 => 4,  26 => 6,  87 => 25,  31 => 4,  25 => 3,  21 => 2,  19 => 1,  93 => 28,  88 => 6,  78 => 21,  46 => 16,  44 => 12,  27 => 4,  79 => 18,  72 => 37,  69 => 25,  47 => 9,  40 => 7,  37 => 10,  22 => 2,  246 => 90,  157 => 56,  145 => 46,  139 => 45,  131 => 52,  123 => 47,  120 => 40,  115 => 43,  111 => 37,  108 => 5,  101 => 32,  98 => 56,  96 => 55,  83 => 25,  74 => 14,  66 => 24,  55 => 15,  52 => 21,  50 => 10,  43 => 6,  41 => 5,  35 => 10,  32 => 4,  29 => 3,  209 => 82,  203 => 78,  199 => 67,  193 => 73,  189 => 71,  187 => 84,  182 => 66,  176 => 64,  173 => 65,  168 => 72,  164 => 59,  162 => 57,  154 => 58,  149 => 51,  147 => 58,  144 => 49,  141 => 48,  133 => 55,  130 => 41,  125 => 44,  122 => 8,  116 => 41,  112 => 42,  109 => 34,  106 => 49,  103 => 32,  99 => 31,  95 => 28,  92 => 53,  86 => 28,  82 => 22,  80 => 19,  73 => 19,  64 => 26,  60 => 6,  57 => 11,  54 => 10,  51 => 14,  48 => 13,  45 => 17,  42 => 13,  39 => 10,  36 => 5,  33 => 6,  30 => 7,);
    }
}
