<?php
namespace DynactiveSoftware\LearningPlatform;

/**
 * Holds the different role constants that a user can have for their role.
 */
class LMSRole {
    
    /**
     * Participates in course work
     */
    const Student = 'STUDENT';
    
    /**
     * Manages students and can view student grades as well as access courses
     */
    const Instructor = "INSTRUCTOR";
    
    /**
     * Can deploy new courses, update existing courses, and manage client settings.
     */
    const ClientAdmin = "CLIENT_ADMIN";

    /**
     * Similar to student role, but progress in the course is not saved.
     */
    const Demo = "DEMO_USER";
}
