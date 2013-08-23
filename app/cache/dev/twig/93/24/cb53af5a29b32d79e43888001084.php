<?php

/* SilvanusFirewallRulesBundle:FirewallRules:new.html.twig */
class __TwigTemplate_9324cb53af5a29b32d79e43888001084 extends Twig_Template
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
        // line 3
        $this->env->getExtension('form')->renderer->setTheme((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), array(0 => $this));
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_form_errors($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        ob_start();
        // line 7
        echo "        ";
        if ((twig_length_filter($this->env, (isset($context["errors"]) ? $context["errors"] : $this->getContext($context, "errors"))) > 0)) {
            // line 8
            echo "
            ";
            // line 9
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["errors"]) ? $context["errors"] : $this->getContext($context, "errors")));
            foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                // line 10
                echo "                <div class=\"label label-danger\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "message"), "html", null, true);
                echo "</div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            echo "        
        ";
        }
        // line 14
        echo "    ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 17
    public function block_form_row($context, array $blocks = array())
    {
        // line 18
        echo "    
    <div class=\"form-group\">
\t\t<div class=\"row\">\t
\t\t\t
\t\t\t<div class=\"col-md-12\">";
        // line 22
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'label');
        echo "</div>
\t\t\t<div class=\"col-md-8\">";
        // line 23
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'widget', array("attr" => array("class" => "form-control input-sm")));
        echo "</div>
\t\t\t<div class=\"col-md-4\">";
        // line 24
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'errors');
        echo "</div>
\t\t\t
\t\t</div>
    </div>
    
";
    }

    // line 31
    public function block_javascripts($context, array $blocks = array())
    {
        // line 32
        echo "
\t<script type=\"text/javascript\">
\t
\t\t\$(document).ready(function(){
\t\t
\t\t\tvar lastPriority=\"\";
\t\t
\t\t\t\$(\"#silvanus_firewallrulesbundle_firewallrulestype_append\").click(function(event){
\t\t\t
\t\t\t\tif(\$(this).is(':checked')){
\t\t\t\t
\t\t\t\t\tlastPriority=\$(\"#silvanus_firewallrulesbundle_firewallrulestype_priority\").val();
\t\t\t\t\t\$(\"#silvanus_firewallrulesbundle_firewallrulestype_priority\").val(\"\");
\t\t\t\t\t\$(\"#silvanus_firewallrulesbundle_firewallrulestype_priority\").attr(\"disabled\",\"disabled\");
\t\t\t\t
\t\t\t\t\t
\t\t\t\t}
\t\t\t\tif(!\$(this).is(':checked')){
\t\t\t\t
\t\t\t\t\t\$(\"#silvanus_firewallrulesbundle_firewallrulestype_priority\").removeAttr(\"disabled\");
\t\t\t\t\t\$(\"#silvanus_firewallrulesbundle_firewallrulestype_priority\").val(lastPriority);
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t
\t\t\t\t}
\t\t\t
\t\t\t});
\t\t
\t\t});
\t
\t</script>

";
    }

    // line 65
    public function block_body($context, array $blocks = array())
    {
        // line 67
        echo "<div class=\"page-header\"><h1>Firewall <small>create new rule</small></h1></div>

    <form action=\"";
        // line 69
        echo $this->env->getExtension('routing')->getPath("firewallrules_create");
        echo "\" method=\"post\" ";
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'enctype');
;
        echo ">
        ";
        // line 70
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'widget');
        echo "
        <p>
            <button class=\"btn btn-success\" type=\"submit\">Create</button>
        </p>
    </form>

\t<div class=\"col-md-12\">\t
\t\t\t<br><br>
\t\t\t
\t\t\t<button class=\"btn btn-info\" onclick=\"window.location.href='";
        // line 79
        echo $this->env->getExtension('routing')->getPath("firewallrules");
        echo "'\">Back</button>
\t\t
\t</div>

";
    }

    public function getTemplateName()
    {
        return "SilvanusFirewallRulesBundle:FirewallRules:new.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  158 => 79,  146 => 70,  139 => 69,  135 => 67,  132 => 65,  97 => 32,  94 => 31,  84 => 24,  80 => 23,  76 => 22,  70 => 18,  67 => 17,  62 => 14,  58 => 12,  49 => 10,  45 => 9,  42 => 8,  39 => 7,  36 => 6,  33 => 5,  28 => 3,);
    }
}
