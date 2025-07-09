# MDRRMO Color Scheme Guide

## 🎨 **Monotone Gray-Green Palette**

The MDRRMO system uses a professional, eye-friendly monotone color scheme based on muted gray-green tones. This approach reduces eye strain and provides a clean, government-appropriate appearance.

---

## 🎯 **Design Philosophy**

### Why Monotone?
- **Reduced Eye Strain**: Subtle color variations are easier on the eyes during long work sessions
- **Professional Appearance**: Government systems require serious, trustworthy design
- **Accessibility**: High contrast ratios and muted tones improve readability
- **Consistency**: Single color family prevents visual conflicts and maintains harmony

### Target Audience
- MDRRMO staff working long hours on emergency responses
- Government officials requiring professional interface design
- Users who need to focus on data rather than colorful distractions

---

## 🎨 **Color Palette**

### Primary Gray-Green Scale
```css
--primary-50: #f8faf9    /* Lightest - backgrounds, subtle areas */
--primary-100: #f1f5f3   /* Very Light - card backgrounds, input fields */
--primary-200: #e2e8e4   /* Light - borders, dividers */
--primary-300: #cbd5d1   /* Medium-Light - disabled states */
--primary-400: #9ca8a3   /* Medium - placeholders, secondary text */
--primary-500: #6b7671   /* MAIN PRIMARY - buttons, links, highlights */
--primary-600: #556159   /* Dark - hover states, active elements */
--primary-700: #424d47   /* Darker - pressed states, emphasis */
--primary-800: #2f3833   /* Very Dark - headings, important text */
--primary-900: #1a201c   /* Darkest - high contrast text */
```

### System Colors (Muted)
```css
--success: #556159      /* Success messages (uses primary-600) */
--warning: #8b7355      /* Warning messages (muted brown-gray) */
--info: #6b7671         /* Info messages (uses primary-500) */
--danger: #7a5a5a       /* Error messages (muted red-gray) */
```

### Neutral Grays (Harmonious)
```css
--gray-50: #fafbfb      /* Backgrounds */
--gray-100: #f4f6f5     /* Card backgrounds */
--gray-200: #e9ece9     /* Borders */
--gray-300: #d3d7d3     /* Input borders */
--gray-400: #a1a6a1     /* Placeholder text */
--gray-500: #6f746f     /* Secondary text */
--gray-600: #5c615c     /* Primary text */
--gray-700: #4a4f4a     /* Headings */
--gray-800: #383d38     /* Important text */
--gray-900: #262a26     /* High contrast text */
```

---

## 🖌️ **Usage Guidelines**

### Primary Actions
```css
/* Buttons, Links, Call-to-Actions */
background-color: var(--primary-500);   /* Main action color */
border-color: var(--primary-500);
color: white;

/* Hover States */
background-color: var(--primary-600);   /* Darker on hover */
```

### Text Hierarchy
```css
/* Headings */
color: var(--primary-800);              /* Dark gray-green for emphasis */

/* Body Text */
color: var(--gray-700);                 /* Standard reading color */

/* Secondary Text */
color: var(--gray-500);                 /* Less important information */

/* Placeholder Text */
color: var(--gray-400);                 /* Form placeholders */
```

### Backgrounds
```css
/* Main Background */
background-color: var(--gray-50);       /* Subtle off-white */

/* Card Backgrounds */
background-color: white;                /* Clean white cards */
background-color: var(--primary-50);    /* Subtle tinted alternative */

/* Input Backgrounds */
background-color: white;                /* Clean input fields */
```

### Borders & Dividers
```css
/* Standard Borders */
border-color: var(--gray-200);          /* Subtle borders */

/* Input Borders */
border-color: var(--gray-300);          /* Slightly more visible */

/* Focus States */
border-color: var(--primary-500);       /* Primary color on focus */
```

---

## 🚨 **System Messages**

### Success Messages
```css
background-color: var(--primary-50);    /* Light green-gray background */
color: var(--primary-800);              /* Dark green-gray text */
border-left: 4px solid var(--success);  /* Success accent border */
```

### Warning Messages
```css
background-color: var(--gray-100);      /* Light gray background */
color: var(--warning);                  /* Muted brown-gray text */
border-left: 4px solid var(--warning);  /* Warning accent border */
```

### Error Messages
```css
background-color: var(--gray-100);      /* Light gray background */
color: var(--danger);                   /* Muted red-gray text */
border-left: 4px solid var(--danger);   /* Error accent border */
```

### Info Messages
```css
background-color: var(--primary-50);    /* Light green-gray background */
color: var(--primary-700);              /* Medium green-gray text */
border-left: 4px solid var(--info);     /* Info accent border */
```

---

## 🎨 **Component Examples**

### Navigation Bar
```css
background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
color: white;
```

### Cards
```css
background: white;
border: 1px solid var(--gray-200);
box-shadow: var(--shadow-sm);
```

### Forms
```css
/* Input Fields */
border: 1px solid var(--gray-300);
background: white;
color: var(--gray-700);

/* Focus State */
border-color: var(--primary-500);
box-shadow: 0 0 0 3px rgba(107, 118, 113, 0.1);
```

### Buttons
```css
/* Primary Button */
background: var(--primary-500);
color: white;
border: 1px solid var(--primary-500);

/* Secondary Button */
background: transparent;
color: var(--primary-600);
border: 1px solid var(--primary-400);
```

---

## 🔧 **Implementation Rules**

### DO's
✅ **Use the defined color variables** - Always reference CSS custom properties  
✅ **Maintain contrast ratios** - Ensure text is readable on backgrounds  
✅ **Stay within the palette** - Don't introduce new colors  
✅ **Use appropriate opacity** - Lighten colors with opacity rather than new colors  
✅ **Test with different screen settings** - Verify colors work in various lighting  

### DON'Ts
❌ **Don't use bright colors** - Avoid #22c55e, #dc2626, or other vivid colors  
❌ **Don't mix color families** - Stay within the gray-green monotone system  
❌ **Don't use pure black/white** - Use the defined gray scale instead  
❌ **Don't create new color variables** - Work within the existing system  
❌ **Don't use gradients with multiple hues** - Keep gradients within the same color family  

---

## 📱 **Responsive Considerations**

### Mobile Devices
- Colors may appear different on smaller screens
- Ensure sufficient contrast for outdoor viewing
- Test on various device types and brightness settings

### Dark Mode (Future)
- Current palette can be inverted for dark mode
- Primary-900 becomes background, Primary-50 becomes text
- Maintain the same monotone philosophy

---

## 🎯 **Accessibility Compliance**

### WCAG 2.1 Guidelines
- **AA Rating**: All text meets minimum contrast requirements
- **Colorblind Friendly**: Gray-green palette works for all color vision types
- **Focus Indicators**: Clear, high-contrast focus states
- **Error Identification**: Errors not identified by color alone

### Testing Tools
- Use WebAIM Contrast Checker for color combinations
- Test with colorblindness simulators
- Verify with actual MDRRMO staff feedback

---

## 🚀 **Migration from Old Colors**

### Replaced Colors
```css
/* OLD: Bright green theme */
#22c55e → var(--primary-500)    /* Main actions */
#16a34a → var(--primary-600)    /* Hover states */
#dcfce7 → var(--primary-100)    /* Light backgrounds */

/* OLD: Bright red alerts */
#dc2626 → var(--danger)         /* Error messages */
#fee2e2 → var(--gray-100)       /* Error backgrounds */
```

### Benefits of Migration
- **Reduced eye strain** from softer colors
- **Better professionalism** for government system
- **Improved accessibility** with better contrast
- **Consistent user experience** across all components

---

This monotone color scheme creates a professional, accessible, and eye-friendly interface perfectly suited for the MDRRMO's serious work environment while maintaining visual appeal and usability. 
