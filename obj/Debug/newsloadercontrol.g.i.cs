﻿#pragma checksum "..\..\newsloadercontrol.xaml" "{ff1816ec-aa5e-4d10-87f7-6f4963833460}" "4B04376270734E2B85D55845173C096041D5EA25"
//------------------------------------------------------------------------------
// <auto-generated>
//     Этот код создан программой.
//     Исполняемая версия:4.0.30319.42000
//
//     Изменения в этом файле могут привести к неправильной работе и будут потеряны в случае
//     повторной генерации кода.
// </auto-generated>
//------------------------------------------------------------------------------

using System;
using System.Diagnostics;
using System.Windows;
using System.Windows.Automation;
using System.Windows.Controls;
using System.Windows.Controls.Primitives;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Ink;
using System.Windows.Input;
using System.Windows.Markup;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Media.Effects;
using System.Windows.Media.Imaging;
using System.Windows.Media.Media3D;
using System.Windows.Media.TextFormatting;
using System.Windows.Navigation;
using System.Windows.Shapes;
using System.Windows.Shell;


namespace Launcher {
    
    
    /// <summary>
    /// NewsLoaderControl
    /// </summary>
    public partial class NewsLoaderControl : System.Windows.Controls.UserControl, System.Windows.Markup.IComponentConnector {
        
        
        #line 6 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal Launcher.NewsLoaderControl NewsControl;
        
        #line default
        #line hidden
        
        
        #line 100 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.Grid LayoutRoot;
        
        #line default
        #line hidden
        
        
        #line 102 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.Label news_indacator_label;
        
        #line default
        #line hidden
        
        
        #line 104 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.TextBlock news_indicator_text;
        
        #line default
        #line hidden
        
        
        #line 106 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.Grid MainGrid;
        
        #line default
        #line hidden
        
        
        #line 107 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.Image news_image;
        
        #line default
        #line hidden
        
        
        #line 108 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Shapes.Rectangle body_bg;
        
        #line default
        #line hidden
        
        
        #line 110 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.StackPanel stackPanel;
        
        #line default
        #line hidden
        
        
        #line 111 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.TextBlock news_head;
        
        #line default
        #line hidden
        
        
        #line 113 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.TextBlock news_body;
        
        #line default
        #line hidden
        
        
        #line 117 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.Button btn_left;
        
        #line default
        #line hidden
        
        
        #line 126 "..\..\newsloadercontrol.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.Button btn_right;
        
        #line default
        #line hidden
        
        private bool _contentLoaded;
        
        /// <summary>
        /// InitializeComponent
        /// </summary>
        [System.Diagnostics.DebuggerNonUserCodeAttribute()]
        [System.CodeDom.Compiler.GeneratedCodeAttribute("PresentationBuildTasks", "4.0.0.0")]
        public void InitializeComponent() {
            if (_contentLoaded) {
                return;
            }
            _contentLoaded = true;
            System.Uri resourceLocater = new System.Uri("/AsgaronLauncher;component/newsloadercontrol.xaml", System.UriKind.Relative);
            
            #line 1 "..\..\newsloadercontrol.xaml"
            System.Windows.Application.LoadComponent(this, resourceLocater);
            
            #line default
            #line hidden
        }
        
        [System.Diagnostics.DebuggerNonUserCodeAttribute()]
        [System.CodeDom.Compiler.GeneratedCodeAttribute("PresentationBuildTasks", "4.0.0.0")]
        [System.ComponentModel.EditorBrowsableAttribute(System.ComponentModel.EditorBrowsableState.Never)]
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Design", "CA1033:InterfaceMethodsShouldBeCallableByChildTypes")]
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Maintainability", "CA1502:AvoidExcessiveComplexity")]
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1800:DoNotCastUnnecessarily")]
        void System.Windows.Markup.IComponentConnector.Connect(int connectionId, object target) {
            switch (connectionId)
            {
            case 1:
            this.NewsControl = ((Launcher.NewsLoaderControl)(target));
            
            #line 6 "..\..\newsloadercontrol.xaml"
            this.NewsControl.MouseLeftButtonDown += new System.Windows.Input.MouseButtonEventHandler(this.NewsControl_MouseLeftButtonDown);
            
            #line default
            #line hidden
            return;
            case 2:
            this.LayoutRoot = ((System.Windows.Controls.Grid)(target));
            return;
            case 3:
            this.news_indacator_label = ((System.Windows.Controls.Label)(target));
            return;
            case 4:
            this.news_indicator_text = ((System.Windows.Controls.TextBlock)(target));
            return;
            case 5:
            this.MainGrid = ((System.Windows.Controls.Grid)(target));
            return;
            case 6:
            this.news_image = ((System.Windows.Controls.Image)(target));
            return;
            case 7:
            this.body_bg = ((System.Windows.Shapes.Rectangle)(target));
            return;
            case 8:
            this.stackPanel = ((System.Windows.Controls.StackPanel)(target));
            return;
            case 9:
            this.news_head = ((System.Windows.Controls.TextBlock)(target));
            return;
            case 10:
            this.news_body = ((System.Windows.Controls.TextBlock)(target));
            return;
            case 11:
            this.btn_left = ((System.Windows.Controls.Button)(target));
            
            #line 117 "..\..\newsloadercontrol.xaml"
            this.btn_left.Click += new System.Windows.RoutedEventHandler(this.btn_left_Click);
            
            #line default
            #line hidden
            return;
            case 12:
            this.btn_right = ((System.Windows.Controls.Button)(target));
            
            #line 126 "..\..\newsloadercontrol.xaml"
            this.btn_right.Click += new System.Windows.RoutedEventHandler(this.btn_right_Click);
            
            #line default
            #line hidden
            return;
            }
            this._contentLoaded = true;
        }
    }
}

